<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/alert', name: 'app_alert_')]
class AlertController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $tab = $request->query->get('tab', 'stock-bas');

        // Produits avec stock bas
        $lowStockProducts = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.stockQuantity <= p.minStockLevel')
            ->andWhere('p.stockQuantity > 0')
            ->andWhere('p.isActive = true')
            ->orderBy('p.stockQuantity', 'ASC')
            ->getQuery()
            ->getResult();

        // Produits en rupture de stock
        $outOfStockProducts = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.stockQuantity = 0')
            ->andWhere('p.isActive = true')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        // Produits proches de la péremption (30 jours)
        $today = new \DateTime();
        $in30Days = (new \DateTime())->modify('+30 days');

        $expiringProducts = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.expiryDate IS NOT NULL')
            ->andWhere('p.expiryDate BETWEEN :today AND :in30days')
            ->andWhere('p.stockQuantity > 0')
            ->setParameter('today', $today)
            ->setParameter('in30days', $in30Days)
            ->orderBy('p.expiryDate', 'ASC')
            ->getQuery()
            ->getResult();

        // Produits périmés
        $expiredProducts = $this->productRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->where('p.expiryDate < :today')
            ->andWhere('p.stockQuantity > 0')
            ->setParameter('today', $today)
            ->orderBy('p.expiryDate', 'ASC')
            ->getQuery()
            ->getResult();

        // Statistiques
        $stats = [
            'low_stock_count' => count($lowStockProducts),
            'out_of_stock_count' => count($outOfStockProducts),
            'expiring_count' => count($expiringProducts),
            'expired_count' => count($expiredProducts),
        ];

        return $this->render('alert/index.html.twig', [
            'page_title' => 'Alertes',
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'expiring_products' => $expiringProducts,
            'expired_products' => $expiredProducts,
            'stats' => $stats,
            'active_tab' => $tab,
        ]);
    }
}