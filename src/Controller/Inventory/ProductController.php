<?php

namespace App\Controller\Inventory;

use App\Entity\Product;
use App\Form\Product\ProductType;
use App\Repository\ProductRepository;
use App\Service\Inventory\StockManagementService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/inventory/product')]
#[IsGranted('ROLE_USER')]
class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private StockManagementService $stockService
    ) {
    }

    /**
     * Liste des produits avec pagination et recherche
     */
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        $category = $request->query->get('category');
        $stockStatus = $request->query->get('stock_status');

        $queryBuilder = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c');

        // Recherche
        if ($search) {
            $queryBuilder->andWhere('p.name LIKE :search OR p.barcode LIKE :search OR p.sku LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Filtrer par catégorie
        if ($category) {
            $queryBuilder->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }

        // Filtrer par statut de stock
        if ($stockStatus === 'low') {
            $queryBuilder->andWhere('p.stockQuantity <= p.minStockLevel')
                ->andWhere('p.stockQuantity > 0');
        } elseif ($stockStatus === 'out') {
            $queryBuilder->andWhere('p.stockQuantity = 0');
        } elseif ($stockStatus === 'ok') {
            $queryBuilder->andWhere('p.stockQuantity > p.minStockLevel');
        }

        $queryBuilder->orderBy('p.name', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('inventory/product/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'category' => $category,
            'stock_status' => $stockStatus,
        ]);
    }

    /**
     * Créer un nouveau produit
     */
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image si présent
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('products_images_directory'),
                    $newFilename
                );
                $product->setImagePath($newFilename);
            }

            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', 'Produit créé avec succès.');

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('inventory/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'isNew' => true,
        ]);
    }

    /**
     * Afficher les détails d'un produit
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $movements = []; // Temporaire - pas de mouvements pour l'instant

        return $this->render('inventory/product/show.html.twig', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }

    /**
     * Modifier un produit
     */
    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image si présent
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                if ($product->getImagePath()) {
                    $oldImagePath = $this->getParameter('products_images_directory') . '/' . $product->getImagePath();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('products_images_directory'),
                    $newFilename
                );
                $product->setImagePath($newFilename);
            }

            $this->em->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('inventory/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'isNew' => false,
        ]);
    }

    /**
     * Supprimer un produit
     */
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            // Vérifier si le produit n'a pas de mouvements de stock ou de ventes
            if ($product->getStockMovements()->count() > 0) {
    $this->addFlash('error', 'Impossible de supprimer ce produit car il a un historique de mouvements.');
    return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
}

            // Supprimer l'image si elle existe
            if ($product->getImagePath()) {
                $imagePath = $this->getParameter('products_images_directory') . '/' . $product->getImagePath();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->em->remove($product);
            $this->em->flush();

            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('app_product_index');
    }

    /**
     * API: Recherche rapide de produits (pour l'autocomplétion)
     */
    #[Route('/api/search', name: 'app_product_api_search', methods: ['GET'])]
    public function apiSearch(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $products = $this->productRepository->createQueryBuilder('p')
            ->where('p.name LIKE :query OR p.barcode LIKE :query OR p.sku LIKE :query')
            ->andWhere('p.isActive = true')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        $results = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'barcode' => $product->getBarcode(),
                'sku' => $product->getSku(),
                'price' => (float) $product->getSellingPrice(),
                'stock' => $product->getStockQuantity(),
                'category' => $product->getCategory()?->getName(),
                'requiresPrescription' => $product->isRequiresPrescription(),
            ];
        }, $products);

        return $this->json($results);
    }

    /**
     * API: Obtenir les informations d'un produit par code-barres
     */
    #[Route('/api/barcode/{barcode}', name: 'app_product_api_barcode', methods: ['GET'])]
    public function apiByBarcode(string $barcode): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['barcode' => $barcode]);

        if (!$product) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'barcode' => $product->getBarcode(),
            'sku' => $product->getSku(),
            'price' => (float) $product->getSellingPrice(),
            'stock' => $product->getStockQuantity(),
            'category' => $product->getCategory()?->getName(),
            'requiresPrescription' => $product->isRequiresPrescription(),
            'taxRate' => (float) $product->getTaxRate(),
            'unit' => $product->getUnit(),
            'isLowStock' => $product->isLowStock(),
            'isExpired' => $product->isExpired(),
        ]);
    }

    /**
     * Basculer le statut actif/inactif
     */
    #[Route('/{id}/toggle-active', name: 'app_product_toggle_active', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleActive(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $product->getId(), $request->request->get('_token'))) {
            $product->setIsActive(!$product->isActive());
            $this->em->flush();

            $status = $product->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Produit $status avec succès.");
        }

        return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
    }

    /**
     * Export des produits en Excel
     */
    #[Route('/export/excel', name: 'app_product_export_excel', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function exportExcel(): Response
    {
        // TODO: Implémenter l'export Excel avec PhpSpreadsheet
        $this->addFlash('info', 'Export Excel en cours de développement.');
        return $this->redirectToRoute('app_product_index');
    }

    /**
     * Imprimer les étiquettes de produits
     */
    #[Route('/print/labels', name: 'app_product_print_labels', methods: ['POST'])]
    public function printLabels(Request $request): Response
    {
        $productIds = $request->request->all('products');
        
        if (empty($productIds)) {
            $this->addFlash('error', 'Aucun produit sélectionné.');
            return $this->redirectToRoute('app_product_index');
        }

        $products = $this->productRepository->findBy(['id' => $productIds]);

        return $this->render('inventory/product/labels.html.twig', [
            'products' => $products,
        ]);
    }
}
