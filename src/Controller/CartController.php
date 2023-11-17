<?php

namespace App\Controller;

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

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(Security $security,CartItemRepository $cartItemRepository
    ,Product $product,EntityManagerInterface $manager): Response
    {
        $logedUser = $security->getUser();
        $existingCartItem = $cartItemRepository->findOneBy([
            'product' => $product,
        ]);
        if ($existingCartItem) {
           
            $manager->remove($existingCartItem);
            $manager->flush();
        } else {
            return $this->redirectToRoute('cart');
        }
        return $this->redirectToRoute('cart');
    }
    #[Route('/cart/quantity/{id}', name: 'cart_quantity')]
    public function newQuanity(Security $security,CartItemRepository $cartItemRepository
    ,Product $product,EntityManagerInterface $manager): Response
    {
        $logedUser = $security->getUser();
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

    
}
