<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/supplier/order', name: 'app_supplier_order_')]
class SupplierOrderController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('supplier_order/index.html.twig', [
            'page_title' => 'Commandes Fournisseurs',
        ]);
    }
}