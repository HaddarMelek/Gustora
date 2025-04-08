<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin_dashboard.html.twig');
    }

    #[Route('/user', name: 'user_dashboard')]
    public function user(): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/', name: 'dashboard')]

    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
        ]);
    }
}
