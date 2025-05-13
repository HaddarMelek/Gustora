<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    private function getDashboardRoute(): string
    {
        return $this->isGranted('ROLE_ADMIN') ? 'admin_dashboard' : 'user_dashboard';
    }

    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function createOrder(
        Request $request,
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager,
        PdfGenerator $pdfGenerator,
        MailerInterface $mailer
    ): Response {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty!');
            return $this->redirectToRoute('app_cart_show');
        }

        // Create new order
        $order = new Order();
        $order->setUser($this->getUser())
              ->setStatus('pending')
              ->setPaymentMethod($request->request->get('paymentMethod', 'Cash on Delivery'));

        // Convert cart items to order items
        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order)
                     ->setProduct($cartItem->getProduct())
                     ->setQuantity($cartItem->getQuantity())
                     ->setPrice($cartItem->getProduct()->getPrice());
            
            $entityManager->persist($orderItem);
        }

        // Generate invoice PDF
        $pdf = $pdfGenerator->generatePdf('invoice/invoice.html.twig', [
            'order' => $order,
            'user' => $this->getUser()
        ]);

        // Save PDF
        $invoicePath = $this->getParameter('kernel.project_dir') . '/public/invoices/';
        if (!is_dir($invoicePath)) {
            mkdir($invoicePath, 0755, true);
        }
        $fileName = sprintf('invoice_%s.pdf', uniqid());
        file_put_contents($invoicePath . $fileName, $pdf);

        // Send confirmation email
        $email = (new TemplatedEmail())
            ->from(new Address('malek.haddar@isimg.tn', 'Gustora'))
            ->to($this->getUser()->getEmail())
            ->subject('Order Confirmation - Gustora')
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'order' => $order,
                'user' => $this->getUser()  // Pass user here as well
            ])
            ->attachFromPath($invoicePath . $fileName);

        $mailer->send($email);

        // Store order ID in session
        $request->getSession()->set('last_order_id', $order->getId());

        // Save order and clear cart
        $entityManager->persist($order);
        $entityManager->remove($cart);
        $entityManager->flush();

        return $this->render('order/success.html.twig', [
            'order' => $order,
            'invoicePath' => '/invoices/' . $fileName
        ]);
    }

    #[Route('/order/success', name: 'app_order_success')]
    public function orderSuccess(Request $request): Response
    {
        // Get order id from session
        $orderId = $request->getSession()->get('last_order_id');
        
        if (!$orderId) {
            return $this->redirectToRoute($this->getDashboardRoute());
        }

        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($orderId);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute($this->getDashboardRoute());
        }

        // Clear the session variable
        $request->getSession()->remove('last_order_id');

        return $this->render('order/success.html.twig', [
            'order' => $order,
            'invoiceUrl' => $this->generateUrl('app_invoice_download', ['id' => $order->getId()])
        ]);
    }

    #[Route('/order/process', name: 'app_order_process', methods: ['POST'])]
    public function processOrder(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        PdfGenerator $pdfGenerator
    ): Response {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty');
            return $this->redirectToRoute('app_cart_show');
        }

        $order = new Order();
        $order->setUser($user)
              ->setStatus(Order::STATUS_PENDING)
              ->setPaymentMethod('not_specified');

        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order)
                      ->setProduct($cartItem->getProduct())
                      ->setQuantity($cartItem->getQuantity())
                      ->setPrice($cartItem->getProduct()->getPrice());

            $order->addItem($orderItem);
        }

        $em->persist($order);
        $em->flush();

        $html = $this->renderView('invoice/invoice.html.twig', [
            'order' => $order,
            'user' => $user
        ]);

        $pdf = $pdfGenerator->generatePdf($html);
        $fileName = sprintf('invoice_%d.pdf', $order->getId());

        $path = $this->getParameter('kernel.project_dir') . '/public/invoices/' . $fileName;
        file_put_contents($path, $pdf);

        $email = (new TemplatedEmail())
            ->from(new Address('malek.haddar@isimg.tn', 'Gustora'))
            ->to($user->getEmail())
            ->subject('Gustora - Order Confirmation #' . $order->getId() . ' - Delivery within 2 hours')
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'order' => $order,
                'user' => $user
            ])
            ->attachFromPath(
                $path,
                'invoice_' . $order->getId() . '.pdf',
                'application/pdf'
            );

        $mailer->send($email);

        $em->remove($cart);
        $em->flush();

        return $this->render('order/success.html.twig', [
            'order' => $order,
            'invoiceUrl' => $this->generateUrl('app_invoice_download', ['id' => $order->getId()])
        ]);
    }
}
