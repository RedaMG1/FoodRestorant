<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $products = $paginator->paginate(
            $productRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/add/{id}', name: 'product_add')]
    public function add(
        Product $product,
        Security $security,
        EntityManagerInterface $manager,
        CartItemRepository $cartItemRepository
    ): Response {
        
        $logedUser = $security->getUser();

        $existingCartItem = $cartItemRepository->findOneBy([
            'product' => $product,

        ]);

        if ($existingCartItem) {
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + 1);
            $manager->persist($existingCartItem);
            $manager->flush();
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setUser($logedUser);
            $cartItem->setQuantity(1);
            $manager->persist($cartItem);
            $manager->flush();
        }


            
    

        return $this->redirectToRoute('product');
    }
}
