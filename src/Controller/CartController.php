<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(
        CartRepository $cartRepository,
        Request $request,
        CartItemRepository $cartItemRepository,
        PaginatorInterface $paginator,
        Security $security
    ): Response {
        $user = $security->getUser();
        if ($user) {
            $cartItems = $cartItemRepository->findBy(['user' => $user]);
            return $this->render('cart/index.html.twig', [
                'cartItems' => $cartItems,
            ]);
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => [],
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(
        Security $security,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $manager,
        $id
    ): Response {
        $existingCartItem = $cartItemRepository->findOneBy(['id' => $id]);
        if ($existingCartItem) {
            $manager->remove($existingCartItem);
            $manager->flush();
        } else {
            return $this->redirectToRoute('cart');
        }
        return $this->redirectToRoute('cart');
    }

    //    when addint to cart from menu
    #[Route('/cart/quantity/{id}', name: 'cart_quantity')]
    public function newQuanity(
        Security $security,
        CartItemRepository $cartItemRepository,
        Product $product,
        EntityManagerInterface $manager
    ): Response {

        $existingCartItem = $cartItemRepository->findOneBy([
            'product' => $product,
        ]);
        if ($existingCartItem) {
            $existingCartItem->setQuantity($existingCartItem->getQuantity());
            $manager->persist($existingCartItem);
            $manager->flush();
        } else {
            return $this->redirectToRoute('cart');
        }
        return $this->redirectToRoute('cart');
    }

    #[Route('/update-quantity/{cartItemId}', name: 'update_quantity')]
    public function updateQuantity(
        Request $request,
        CartItemRepository $cartItemRepository,
        EntityManagerInterface $manager,
        int $cartItemId
    ): Response {
        // Retrieve the selected quantity from the form submission
        $selectedQuantity = $request->request->get('quantity');

        $existingCartItem = $cartItemRepository->findOneBy([
            'id' => $cartItemId,
        ]);

        $existingCartItem->setQuantity($selectedQuantity);
        $manager->persist($existingCartItem);
        $manager->flush();

        return $this->redirectToRoute('cart');
    }
}
