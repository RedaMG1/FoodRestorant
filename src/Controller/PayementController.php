<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Cart;
use App\Entity\Ordering;
use App\Entity\User;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayementController extends AbstractController
{
    private UrlGeneratorInterface $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    #[Route('/payement', name: 'app_payement')]
    public function index(): Response
    {

        return $this->render('payement/index.html.twig', []);
    }


    #[Route('/order/create-session-stripe/{id}', name: 'payement_stripe')]
    public function stripeCheckout(
        $id,

        EntityManagerInterface $manager,
        CartItemRepository $cartItemRepository,
        OrderingRepository $orderingRepository
    ): RedirectResponse // because we dont render we just redirect
    {
        $productStripe = [];

        $order = $orderingRepository->findOneBy(['user' => $id]);
        if (!$order) {
            return $this->redirectToRoute('cart');
        }
        $cart = $order->getCart();
        $cartItems = $cartItemRepository->findBy(['user' => $id]);
        foreach ($cartItems as $cartItem) {

            $product = $cartItem->getProduct();
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice() * 100,
                    'product_data' => [
                        'name' => $product->getName(),
                    ]
                ],
                'quantity' => $cartItem->getQuantity(),
            ];
        }


        Stripe::setApiKey('sk_test_51OCLuWJGgZyU8fMmIb7FsQIpWxO6SRsFjdyMLW6hlUBHo2Yo7u9x9Tgszz93yxtZLfPCBkhS1f4AXf6KqAcmCM4p00bJhsI0OS');

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $cart->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[$productStripe]],
            'mode' => 'payment',
            'success_url' => $this->generator->generate(
                'payement_success',
                ['id' => $order->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->generator->generate(
                'payement_cancel',
                ['id' => $order->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);

        return $this->redirect($checkout_session->url);
    }

    #[Route('/payement_success/{id}', name: 'payement_success')]
    public function stripeSuccess(
        $id,
        Security $security,
        EntityManagerInterface $manager,
        CartItemRepository $cartItemRepository
    ): RedirectResponse {
        $loggedUser = $security->getUser();
        $cartItems = $cartItemRepository->findBy(['user' => $loggedUser]);
        foreach ($cartItems as $cartItem) {
            $manager->remove($cartItem);
            $manager->flush();
        }


        return $this->redirectToRoute('product');
    }
    #[Route('/payement_cancel/{id}', name: 'payement_cancel')]
    public function stripeCancel($id): RedirectResponse
    {
        return $this->render('payement/cancel.html.twig');
    }
}
