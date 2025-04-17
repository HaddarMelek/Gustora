<?php

namespace App\Controller;

use App\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'app_booking')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Handle the booking (save to database, send email, etc.)
            $this->addFlash('success', 'Your booking has been received. We will contact you shortly.');
            return $this->redirectToRoute('app_booking');
        }

        return $this->render('pages/booking.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}