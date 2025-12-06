<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\StockMovement;
use App\Form\StockMovementType;
use App\Repository\StockMovementRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventory/stock-movement', name: 'app_stock_')]
class StockMovementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private StockMovementRepository $stockMovementRepository,
        private ProductRepository $productRepository
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $type = $request->query->get('type', 'all');
        $productId = $request->query->get('product');

        $queryBuilder = $this->stockMovementRepository->createQueryBuilder('sm')
            ->leftJoin('sm.product', 'p')
            ->addSelect('p')
            ->orderBy('sm.createdAt', 'DESC');

        // Filtrer par type
        if ($type !== 'all') {
            $queryBuilder->andWhere('sm.type = :type')
                ->setParameter('type', $type);
        }

        // Filtrer par produit
        if ($productId) {
            $queryBuilder->andWhere('sm.product = :product')
                ->setParameter('product', $productId);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            50
        );

        // Statistiques
        $stats = [
            'total_movements' => $this->stockMovementRepository->count([]),
            'total_in' => $this->stockMovementRepository->count(['type' => 'in']),
            'total_out' => $this->stockMovementRepository->count(['type' => 'out']),
            'total_adjustment' => $this->stockMovementRepository->count(['type' => 'adjustment']),
        ];

        // Produits pour le filtre
        $products = $this->productRepository->findBy([], ['name' => 'ASC']);

        return $this->render('stock_movement/index.html.twig', [
            'page_title' => 'Mouvements de Stock',
            'pagination' => $pagination,
            'stats' => $stats,
            'products' => $products,
            'filter_type' => $type,
            'filter_product' => $productId,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $stockMovement = new StockMovement();
        $form = $this->createForm(StockMovementType::class, $stockMovement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $stockMovement->getProduct();
            $quantity = $stockMovement->getQuantity();
            $type = $stockMovement->getType();

            // Mettre à jour le stock du produit
            if ($type === 'in') {
                $product->setStockQuantity($product->getStockQuantity() + $quantity);
            } elseif ($type === 'out') {
                $newStock = $product->getStockQuantity() - $quantity;
                if ($newStock < 0) {
                    $this->addFlash('error', 'Stock insuffisant pour cette opération.');
                    return $this->render('stock_movement/edit.html.twig', [
                        'stock_movement' => $stockMovement,
                        'form' => $form,
                        'isNew' => true,
                    ]);
                }
                $product->setStockQuantity($newStock);
            } elseif ($type === 'adjustment') {
                $product->setStockQuantity($quantity);
            }

            $this->em->persist($stockMovement);
            $this->em->flush();

            $this->addFlash('success', 'Mouvement de stock enregistré avec succès.');

            return $this->redirectToRoute('app_stock_index');
        }

        return $this->render('stock_movement/edit.html.twig', [
            'stock_movement' => $stockMovement,
            'form' => $form,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(StockMovement $stockMovement): Response
    {
        return $this->render('stock_movement/show.html.twig', [
            'stock_movement' => $stockMovement,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, StockMovement $stockMovement): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stockMovement->getId(), $request->request->get('_token'))) {
            $this->em->remove($stockMovement);
            $this->em->flush();

            $this->addFlash('success', 'Mouvement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_stock_index');
    }
}