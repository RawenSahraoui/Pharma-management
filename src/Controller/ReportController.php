<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/report', name: 'app_report_')]
class ReportController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private SupplierRepository $supplierRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $period = $request->query->get('period', '30days');
        
        $endDate = new \DateTime();
        $startDate = match($period) {
            '7days' => (new \DateTime())->modify('-7 days'),
            '30days' => (new \DateTime())->modify('-30 days'),
            '90days' => (new \DateTime())->modify('-90 days'),
            '1year' => (new \DateTime())->modify('-1 year'),
            default => (new \DateTime())->modify('-30 days'),
        };

        $stats = [
            'total_products' => $this->productRepository->count([]),
            'active_products' => $this->productRepository->count(['isActive' => true]),
            'low_stock_products' => $this->getLowStockCount(),
            'total_suppliers' => $this->supplierRepository->count([]),
            'active_suppliers' => $this->supplierRepository->count(['isActive' => true]),
            'total_categories' => $this->categoryRepository->count([]),
        ];

        $stockValue = $this->getTotalStockValue();

        $topProducts = $this->productRepository->createQueryBuilder('p')
            ->orderBy('p.stockQuantity', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $outOfStock = $this->productRepository->createQueryBuilder('p')
            ->where('p.stockQuantity = 0')
            ->orderBy('p.name', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $categoryStats = $this->getCategoryStats();

        return $this->render('report/index.html.twig', [
            'page_title' => 'Rapports et Statistiques',
            'stats' => $stats,
            'stock_value' => $stockValue,
            'top_products' => $topProducts,
            'out_of_stock' => $outOfStock,
            'category_stats' => $categoryStats,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    #[Route('/inventory', name: 'inventory')]
    public function inventory(): Response
    {
        $products = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->orderBy('p.stockQuantity', 'DESC')
            ->getQuery()
            ->getResult();

        $totalValue = 0;
        foreach ($products as $product) {
            $totalValue += $product->getStockQuantity() * $product->getPurchasePrice();
        }

        return $this->render('report/inventory.html.twig', [
            'page_title' => 'Rapport d\'Inventaire',
            'products' => $products,
            'total_value' => $totalValue,
        ]);
    }

    #[Route('/stock-movement', name: 'stock_movement')]
    public function stockMovement(): Response
    {
        $movements = $this->em->getRepository(\App\Entity\StockMovement::class)
            ->createQueryBuilder('sm')
            ->leftJoin('sm.product', 'p')
            ->addSelect('p')
            ->orderBy('sm.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        return $this->render('report/stock_movement.html.twig', [
            'page_title' => 'Mouvements de Stock',
            'movements' => $movements,
        ]);
    }

    #[Route('/suppliers', name: 'suppliers')]
    public function suppliers(): Response
    {
        $suppliers = $this->supplierRepository->findAll();

        return $this->render('report/suppliers.html.twig', [
            'page_title' => 'Rapport Fournisseurs',
            'suppliers' => $suppliers,
        ]);
    }

    private function getLowStockCount(): int
    {
        return $this->productRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.stockQuantity <= p.minStockLevel')
            ->andWhere('p.stockQuantity > 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getTotalStockValue(): float
    {
        $result = $this->em->createQuery(
            'SELECT SUM(p.stockQuantity * p.purchasePrice) FROM App\Entity\Product p'
        )->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    private function getCategoryStats(): array
    {
        $categories = $this->categoryRepository->findAll();
        $stats = [];

        foreach ($categories as $category) {
            $productCount = $this->productRepository->count(['category' => $category]);
            
            if ($productCount > 0) {
                $stats[] = [
                    'name' => $category->getName(),
                    'count' => $productCount,
                ];
            }
        }

        return $stats;
    }
}