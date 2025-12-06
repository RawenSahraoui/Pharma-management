<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventory/expiry', name: 'app_expiry_')]
class ExpiryController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->get('filter', 'all');
        
        $queryBuilder = $this->productRepository->createQueryBuilder('p')
            ->where('p.expiryDate IS NOT NULL')
            ->orderBy('p.expiryDate', 'ASC');

        $today = new \DateTime();
        $in30Days = (new \DateTime())->modify('+30 days');
        $in90Days = (new \DateTime())->modify('+90 days');

        // Filtrer selon le statut
        if ($filter === 'expired') {
            // Produits déjà périmés
            $queryBuilder->andWhere('p.expiryDate < :today')
                ->setParameter('today', $today);
        } elseif ($filter === 'expiring_soon') {
            // Produits expirant dans les 30 jours
            $queryBuilder->andWhere('p.expiryDate >= :today')
                ->andWhere('p.expiryDate <= :in30days')
                ->setParameter('today', $today)
                ->setParameter('in30days', $in30Days);
        } elseif ($filter === 'expiring_3months') {
            // Produits expirant dans les 90 jours
            $queryBuilder->andWhere('p.expiryDate >= :today')
                ->andWhere('p.expiryDate <= :in90days')
                ->setParameter('today', $today)
                ->setParameter('in90days', $in90Days);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        // Statistiques
        $stats = [
            'expired' => $this->productRepository->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->where('p.expiryDate < :today')
                ->setParameter('today', $today)
                ->getQuery()
                ->getSingleScalarResult(),
            
            'expiring_30days' => $this->productRepository->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->where('p.expiryDate >= :today')
                ->andWhere('p.expiryDate <= :in30days')
                ->setParameter('today', $today)
                ->setParameter('in30days', $in30Days)
                ->getQuery()
                ->getSingleScalarResult(),
            
            'expiring_90days' => $this->productRepository->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->where('p.expiryDate >= :today')
                ->andWhere('p.expiryDate <= :in90days')
                ->setParameter('today', $today)
                ->setParameter('in90days', $in90Days)
                ->getQuery()
                ->getSingleScalarResult(),
        ];

        return $this->render('expiry/index.html.twig', [
            'page_title' => 'Produits Périmés',
            'pagination' => $pagination,
            'filter' => $filter,
            'stats' => $stats,
        ]);
    }

    #[Route('/remove-expired', name: 'remove_expired', methods: ['POST'])]
    public function removeExpired(Request $request): Response
    {
        $productId = $request->request->get('product_id');
        
        if (!$productId) {
            $this->addFlash('error', 'Produit non spécifié.');
            return $this->redirectToRoute('app_expiry_index');
        }

        $product = $this->productRepository->find($productId);
        
        if (!$product) {
            $this->addFlash('error', 'Produit introuvable.');
            return $this->redirectToRoute('app_expiry_index');
        }

        // Mettre le stock à 0
        $product->setStockQuantity(0);
        $product->setIsActive(false);
        $this->em->flush();

        $this->addFlash('success', "Le produit {$product->getName()} a été retiré du stock.");

        return $this->redirectToRoute('app_expiry_index');
    }
}