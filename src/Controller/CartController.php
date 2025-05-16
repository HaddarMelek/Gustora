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
use Symfony\Component\HttpFoundation\JsonResponse;

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

    #[Route('/cart/update/{id}', name: 'app_cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(
        Request $request,
        CartItem $cartItem,
        EntityManagerInterface $em
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        try {
            $data = json_decode($request->getContent(), true);
            $newQuantity = (int) $data['quantity'];
            
            // Validate quantity
            if ($newQuantity <= 0) {
                throw new \InvalidArgumentException('Quantity must be greater than 0');
            }
            
            if ($newQuantity > $cartItem->getProduct()->getStock()) {
                throw new \InvalidArgumentException('Not enough items in stock');
            }
            
            // Update quantity
            $cartItem->setQuantity($newQuantity);
            $em->flush();
            
            // Return updated cart total
            return $this->json([
                'success' => true,
                'newTotal' => $cartItem->getCart()->getTotal(),
                'message' => 'Quantity updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
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