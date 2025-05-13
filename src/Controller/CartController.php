<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(
        int $id,
        Request $request,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Check if product is in stock
        if ($product->getStock() <= 0) {
            $this->addFlash('error', 'Sorry, this product is out of stock!');
            return $this->redirectToRoute('app_products_public');
        }

        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]) 
            ?? (new Cart())->setUser($this->getUser());

        $cartItem = $cart->getItems()->filter(
            fn($item) => $item->getProduct() === $product
        )->first() ?: false;

        // Calculate new quantity
        $newQuantity = $cartItem ? $cartItem->getQuantity() + 1 : 1;

        // Check if new quantity exceeds stock
        if ($newQuantity > $product->getStock()) {
            $this->addFlash('error', 'Sorry, only ' . $product->getStock() . ' items available in stock!');
            return $this->redirectToRoute('app_products_public');
        }

        if ($cartItem) {
            $cartItem->setQuantity($newQuantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product)
                    ->setQuantity(1)
                    ->setCart($cart);
            $cart->addItem($cartItem);
        }

        $entityManager->persist($cart);
        $entityManager->flush();

        $this->addFlash('success', 'Product added to cart!');
        return $this->redirectToRoute('app_products_public');
    }

    #[Route('/cart', name: 'app_cart_show')]
    public function showCart(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);

        return $this->render('cart/show.html.twig', [
            'cart' => $cart
        ]);
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function updateQuantity(
        CartItem $cartItem,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $quantity = (int) $request->request->get('quantity', 1);
        
        // Check stock availability
        if ($quantity > $cartItem->getProduct()->getStock()) {
            $this->addFlash('error', 'Sorry, only ' . $cartItem->getProduct()->getStock() . ' items available in stock!');
            return $this->redirectToRoute('app_cart_show');
        }

        if ($quantity < 1) {
            $entityManager->remove($cartItem);
        } else {
            $cartItem->setQuantity($quantity);
        }
        
        $entityManager->flush();
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeItem(
        CartItem $cartItem,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($cartItem);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clearCart(
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        if ($cart) {
            $entityManager->remove($cart);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/validate', name: 'app_validate_cart')]
    public function validateCart(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);

        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty!');
            return $this->redirectToRoute('app_cart_show');
        }

        return $this->render('cart/validate.html.twig', [
            'cart' => $cart,
            'total' => $cart->getTotal()
        ]);
    }
}