<?php

namespace App\Controller;

use App\Repository\ChefRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChefController extends AbstractController
{
    #[Route('/team', name: 'app_team')]
    public function index(ChefRepository $chefRepository): Response
    {
        return $this->render('pages/team.html.twig', [
            'chefs' => $chefRepository->findAll()
        ]);
    }
}