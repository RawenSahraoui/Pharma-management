<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/prescription', name: 'app_prescription_')]
#[IsGranted('ROLE_ADMIN')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('prescription/index.html.twig', [
            'page_title' => 'Prescriptions',
        ]);
    }
}