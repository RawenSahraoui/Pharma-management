<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function settings(): Response
    {
        return $this->render('settings/settings.html.twig', [
            'page_title' => 'Paramètres',
        ]);
    }

    #[Route('/admin/settings', name: 'app_settings_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', [
            'page_title' => 'Paramètres Système',
        ]);
    }
}