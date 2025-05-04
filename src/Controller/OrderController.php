<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'app_order_create', methods: ['POST'])]
    public function createOrder(
        Request $request,
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty!');
            return $this->redirectToRoute('app_cart_show');
        }

        $order = new Order();
        $order->setUser($this->getUser())
              ->setStatus('pending')
              ->setPaymentMethod($request->request->get('paymentMethod'));

        // Convert cart items to order items
        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order)
                     ->setProduct($cartItem->getProduct())
                     ->setQuantity($cartItem->getQuantity())
                     ->setPrice($cartItem->getProduct()->getPrice());
            
            $order->addItem($orderItem);
        }

        // Calculate total
        $order->setTotalAmount($cart->getTotal());

        // Save order and clear cart
        $entityManager->persist($order);
        $entityManager->remove($cart);
        $entityManager->flush();

        $this->addFlash('success', 'Your order has been placed successfully!');
        return $this->redirectToRoute('app_order_success');
    }

    #[Route('/order/success', name: 'app_order_success')]
    public function orderSuccess(): Response
    {
        return $this->render('order/success.html.twig');
    }
}