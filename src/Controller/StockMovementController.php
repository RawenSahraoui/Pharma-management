<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inventory/stock-movement', name: 'app_stock_')]
class StockMovementController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('stock_movement/index.html.twig', [
            'page_title' => 'Mouvements de Stock',
        ]);
    }
}