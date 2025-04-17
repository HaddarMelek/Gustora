<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/team', name: 'app_team')]
    public function index(): Response
    {
        $team_members = [
            [
                'image' => 'team-1.jpg',
                'name' => 'John Doe',
                'designation' => 'Master Chef',
                'delay' => '0.1',
            ],
            [
                'image' => 'team-2.jpg',
                'name' => 'Jane Smith',
                'designation' => 'Executive Chef',
                'delay' => '0.3',
            ],
            // Add more team members here
        ];

        return $this->render('pages/team.html.twig', [
            'team_members' => $team_members
        ]);
    }
}