<?php

namespace App\Controller\Sales;

use App\Entity\Sale;
use App\Entity\SaleItem;
use App\Entity\Payment;
use App\Entity\Product;
use App\Enum\PaymentMethod;
use App\Repository\ProductRepository;
use App\Service\Sales\POSService;
use App\Service\Sales\InvoiceGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sales/pos')]
#[IsGranted('ROLE_CASHIER')]
class POSController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private POSService $posService,
        private InvoiceGeneratorService $invoiceGenerator
    ) {
    }

    /**
     * Interface du point de vente
     */
    #[Route('/', name: 'app_pos_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('sales/pos/index.html.twig');
    }

    /**
     * Créer une nouvelle vente
     */
    #[Route('/create', name: 'app_pos_create_sale', methods: ['POST'])]
    public function createSale(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['items']) || empty($data['items'])) {
                return $this->json(['error' => 'Aucun article dans la vente'], 400);
            }

            // Créer la vente
            $sale = $this->posService->createSale(
                $this->getUser(),
                $data['items'],
                $data['customer_name'] ?? null,
                $data['customer_phone'] ?? null,
                $data['notes'] ?? null
            );

            // Créer le paiement
            $paymentMethod = PaymentMethod::from($data['payment_method'] ?? 'cash');
            $amountPaid = (float) ($data['amount_paid'] ?? $sale->getTotalAmount());

            $payment = $this->posService->processPayment(
                $sale,
                $paymentMethod,
                $amountPaid,
                $data['payment_reference'] ?? null
            );

            // Générer la facture/reçu
            $invoiceNumber = $this->invoiceGenerator->generateInvoiceNumber();
            $this->invoiceGenerator->generateInvoice($sale, $invoiceNumber);

            return $this->json([
                'success' => true,
                'sale_id' => $sale->getId(),
                'invoice_number' => $invoiceNumber,
                'total' => $sale->getTotalAmount(),
                'paid' => $payment->getAmount(),
                'change' => $payment->getChangeAmount(),
                'message' => 'Vente créée avec succès',
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Rechercher un produit
     */
    #[Route('/search-product', name: 'app_pos_search_product', methods: ['GET'])]
    public function searchProduct(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $products = $this->productRepository->searchForPOS($query);

        $results = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'barcode' => $product->getBarcode(),
                'price' => (float) $product->getSellingPrice(),
                'stock' => $product->getStockQuantity(),
                'taxRate' => (float) $product->getTaxRate(),
                'unit' => $product->getUnit(),
                'requiresPrescription' => $product->isRequiresPrescription(),
                'category' => $product->getCategory()?->getName(),
                'isLowStock' => $product->isLowStock(),
                'isExpired' => $product->isExpired(),
            ];
        }, $products);

        return $this->json($results);
    }

    /**
     * Scanner un code-barres
     */
    #[Route('/scan-barcode', name: 'app_pos_scan_barcode', methods: ['POST'])]
    public function scanBarcode(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $barcode = $data['barcode'] ?? '';

        if (empty($barcode)) {
            return $this->json(['error' => 'Code-barres invalide'], 400);
        }

        $product = $this->productRepository->findOneBy([
            'barcode' => $barcode,
            'isActive' => true
        ]);

        if (!$product) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        if ($product->getStockQuantity() <= 0) {
            return $this->json(['error' => 'Produit en rupture de stock'], 400);
        }

        if ($product->isExpired()) {
            return $this->json(['error' => 'Produit périmé'], 400);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'barcode' => $product->getBarcode(),
            'price' => (float) $product->getSellingPrice(),
            'stock' => $product->getStockQuantity(),
            'taxRate' => (float) $product->getTaxRate(),
            'unit' => $product->getUnit(),
            'requiresPrescription' => $product->isRequiresPrescription(),
            'category' => $product->getCategory()?->getName(),
        ]);
    }

    /**
     * Vérifier la disponibilité du stock
     */
    #[Route('/check-stock', name: 'app_pos_check_stock', methods: ['POST'])]
    public function checkStock(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $items = $data['items'] ?? [];

        $stockIssues = [];

        foreach ($items as $item) {
            $product = $this->productRepository->find($item['product_id']);
            
            if (!$product) {
                $stockIssues[] = [
                    'product_id' => $item['product_id'],
                    'message' => 'Produit non trouvé'
                ];
                continue;
            }

            if ($product->getStockQuantity() < $item['quantity']) {
                $stockIssues[] = [
                    'product_id' => $product->getId(),
                    'product_name' => $product->getName(),
                    'requested' => $item['quantity'],
                    'available' => $product->getStockQuantity(),
                    'message' => 'Stock insuffisant'
                ];
            }

            if ($product->isExpired()) {
                $stockIssues[] = [
                    'product_id' => $product->getId(),
                    'product_name' => $product->getName(),
                    'message' => 'Produit périmé'
                ];
            }
        }

        return $this->json([
            'has_issues' => !empty($stockIssues),
            'issues' => $stockIssues
        ]);
    }

    /**
     * Calculer le total de la vente
     */
    #[Route('/calculate-total', name: 'app_pos_calculate_total', methods: ['POST'])]
    public function calculateTotal(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $items = $data['items'] ?? [];
        $discount = (float) ($data['discount'] ?? 0);

        $subtotal = 0;
        $totalTax = 0;
        $itemsDetails = [];

        foreach ($items as $item) {
            $product = $this->productRepository->find($item['product_id']);
            
            if (!$product) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $product->getSellingPrice();
            $itemTotal = $unitPrice * $quantity;
            
            $taxRate = (float) $product->getTaxRate();
            $taxAmount = $itemTotal * ($taxRate / 100);
            
            $subtotal += $itemTotal;
            $totalTax += $taxAmount;

            $itemsDetails[] = [
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_rate' => $taxRate,
                'tax_amount' => round($taxAmount, 2),
                'subtotal' => round($itemTotal, 2),
                'total' => round($itemTotal + $taxAmount, 2),
            ];
        }

        $total = $subtotal + $totalTax;
        $discountAmount = $total * ($discount / 100);
        $finalTotal = $total - $discountAmount;

        return $this->json([
            'subtotal' => round($subtotal, 2),
            'tax' => round($totalTax, 2),
            'discount_percentage' => $discount,
            'discount_amount' => round($discountAmount, 2),
            'total' => round($finalTotal, 2),
            'items' => $itemsDetails,
        ]);
    }

    /**
     * Annuler une vente
     */
    #[Route('/{id}/cancel', name: 'app_pos_cancel_sale', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function cancelSale(Request $request, Sale $sale): JsonResponse
    {
        if (!$this->isCsrfTokenValid('cancel_sale' . $sale->getId(), $request->request->get('_token'))) {
            return $this->json(['error' => 'Token CSRF invalide'], 403);
        }

        try {
            $reason = $request->request->get('reason', 'Annulation manuelle');
            $this->posService->cancelSale($sale, $this->getUser(), $reason);

            return $this->json([
                'success' => true,
                'message' => 'Vente annulée avec succès'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Imprimer le reçu d'une vente
     */
    #[Route('/{id}/print-receipt', name: 'app_pos_print_receipt', methods: ['GET'])]
    public function printReceipt(Sale $sale): Response
    {
        return $this->render('sales/pos/receipt.html.twig', [
            'sale' => $sale,
        ]);
    }

    /**
     * Obtenir les statistiques du jour
     */
    #[Route('/daily-stats', name: 'app_pos_daily_stats', methods: ['GET'])]
    public function dailyStats(): JsonResponse
    {
        $stats = $this->posService->getDailyStatistics();

        return $this->json($stats);
    }

    /**
     * Fermer la caisse
     */
    #[Route('/close-register', name: 'app_pos_close_register', methods: ['POST'])]
    #[IsGranted('ROLE_CASHIER')]
    public function closeRegister(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $closingReport = $this->posService->closeRegister(
                $this->getUser(),
                (float) $data['cash_in_drawer'],
                $data['notes'] ?? null
            );

            return $this->json([
                'success' => true,
                'report' => $closingReport,
                'message' => 'Caisse fermée avec succès'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Ouvrir la caisse
     */
    #[Route('/open-register', name: 'app_pos_open_register', methods: ['POST'])]
    #[IsGranted('ROLE_CASHIER')]
    public function openRegister(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $this->posService->openRegister(
                $this->getUser(),
                (float) ($data['opening_cash'] ?? 0)
            );

            return $this->json([
                'success' => true,
                'message' => 'Caisse ouverte avec succès'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
