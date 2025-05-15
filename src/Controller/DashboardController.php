<?php

namespace App\Controller;

use App\Security\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    public function __construct(
        private OTPService $otpService
    ) {}

    #[Route('/admin', name: 'admin_dashboard')]
    public function admin(Request $request): Response
    {
        $user = $this->getUser();
        
       

        return $this->render('dashboard/admin_dashboard.html.twig', [
            'user' => $user,
            'category_management_link' => $this->generateUrl('app_category_index'),
        ]);
    }

    #[Route('/user', name: 'user_dashboard')]
    public function user(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            // Rediriger selon le rôle
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            } else {
                return $this->redirectToRoute('user_dashboard');
            }
        }

        return $this->render('home/index.html.twig');
    }
}
