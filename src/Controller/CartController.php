<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartRepository $cartRepository
    ,Request $request,CartItemRepository $cartItemRepository,
    PaginatorInterface $paginator): Response
    {
        $cart = $paginator->paginate(
            $cartItemRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }
}
