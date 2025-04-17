<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        $services = [
            [
                'icon' => 'fa-user-tie',
                'title' => 'Master Chefs',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
                'delay' => '0.1'
            ],
            [
                'icon' => 'fa-utensils',
                'title' => 'Quality Food',
                'description' => 'Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam',
                'delay' => '0.3'
            ],
            // Add more services here
        ];

        return $this->render('pages/service.html.twig', [
            'services' => $services
        ]);
    }
}