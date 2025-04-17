<?php

namespace App\Controller;

use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial')]
    public function index(TestimonialRepository $testimonialRepository): Response
    {
        return $this->render('pages/testimonial.html.twig', [
            'testimonials' => $testimonialRepository->findAll()
        ]);
    }
}