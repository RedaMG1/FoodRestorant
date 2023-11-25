<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Ordering;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(
        Request $request,
        OrderingRepository $orderingRepository,
        CartRepository $cartRepository,
        AddressRepository $addressRepository,
        Security $security,
        EntityManagerInterface $manager,
        CartItemRepository $cartItemRepository,
    ): Response {
        $loggedUser = $security->getUser();
        $currentDate = date('Y-m-d H:i:s');
        $order = $orderingRepository->findOneBy(['user' => $loggedUser]);
        $existingAddress = $addressRepository->findOneBy([
            'user' => $loggedUser,
        ]);

        $cartItems = $cartItemRepository->findBy(['user' => $loggedUser]);
        $cart = $cartRepository->findOneBy([
            'user' => $loggedUser,
        ]);

        if (!$cart) {

            $cart = new Cart();
            $cart->setUser($loggedUser);
        }
        $cartItems = $cartItemRepository->findBy(['user' => $loggedUser]);

        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
        }
        $cartItems = $cartItemRepository->findBy(['user' => $loggedUser]);

        $cart->setAmount($totalAmount);
        $manager->persist($cart);
        $manager->flush();

        if (!$order) {
            $order = new Ordering();
            $order->setCreatedAt($currentDate);
            $order->setUser($loggedUser);
            $order->setCart($cart);
            $manager->persist($order);
            $manager->flush();
        }

        return $this->render('checkout/index.html.twig', [
            'address' => $existingAddress,
            'user' => $loggedUser,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    #[Route('/order/create', name: 'order_create')]
    public function createOrder(
        EntityManagerInterface $manager,
        Security $security,
        OrderingRepository $orderingRepository,
        CartRepository $cartRepository,
    ): Response // because we dont render we just redirect
    {
        $order = new Ordering();
        $loggedUser = $security->getUser();
        $currentDate = date('Y-m-d H:i:s');
        $cart = $cartRepository->findOneBy(['user' => $loggedUser]);
        $order->setCreatedAt($currentDate);
        $order->setUser($loggedUser);
        $order->setCart($cart);
        $manager->persist($order);
        $manager->flush();
        return $this->redirectToRoute('payement');
    }
}
