<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Cart;
use App\Entity\User;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayementController extends AbstractController
{
    #[Route('/payement', name: 'app_payement')]
    public function index(
        Request $request,CartRepository $cartRepository,
        AddressRepository $addressRepository,
        Security $security,EntityManagerInterface $manager,
        CartItemRepository $cartItemRepository,
    ): Response {
        $loggedUser = $security->getUser();
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
        return $this->render('payement/index.html.twig', [
            'address' => $existingAddress,
            'user' => $loggedUser,
            'cartItems' => $cartItems,
            'totalAmount'=>$totalAmount,
        ]);
    }


    // #[Route('/checkout', name: 'app_checkout')]
    // public function checkout(): Response
    // {

    // }
}
