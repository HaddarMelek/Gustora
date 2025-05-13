<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class InvoiceController extends AbstractController
{
    public function __construct(private PdfGenerator $pdfGenerator) {}

    #[Route('/invoice/download/{id}', name: 'app_invoice_download')]
    public function download(Order $order): Response
    {
        // Check if user has access to this order
        if ($order->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You cannot access this invoice.');
        }

        // Generate PDF
        $pdf = $this->pdfGenerator->generatePdf('invoice/invoice.html.twig', [
            'order' => $order,
            'user' => $order->getUser()
        ]);

        // Create response with PDF headers
        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 
            'attachment; filename="invoice_' . $order->getId() . '.pdf"'
        );

        return $response;
    }
}