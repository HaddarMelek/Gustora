<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestimonialController extends AbstractController
{
    #[Route('/testimonial', name: 'app_testimonial')]
    public function index(): Response
    {
        $testimonials = [
            [
                'content' => 'Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam',
                'image' => 'testimonial-1.jpg',
                'name' => 'Client Name',
                'profession' => 'Profession'
            ],
            [
                'content' => 'Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam',
                'image' => 'testimonial-2.jpg',
                'name' => 'Client Name',
                'profession' => 'Profession'
            ],
            // Add more testimonials as needed
        ];

        return $this->render('pages/testimonial.html.twig', [
            'testimonials' => $testimonials
        ]);
    }
}