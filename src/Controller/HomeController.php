<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ServiceRepository;
use App\Repository\TestimonialRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(ServiceRepository $serviceRepository, TestimonialRepository $testimonialRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $serviceRepository->findAll(),
            'testimonials' => $testimonialRepository->findAll(),
        ]);

    }
}