<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\CartItemRepository;
use App\Repository\CategoryRepository;
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
        Request $request,
        CategoryRepository $categoryRepository
    ): Response {
        $products = $paginator->paginate(
            $productRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        $categorys = 
            $categoryRepository->findAll();
            
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categorys' => $categorys,
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

    #[Route('/product/{name}', name: 'product_details')]
    public function details(
        Product $product,
        string $name,
        ProductRepository $productRepository
    ): Response {

        $product = $productRepository->findOneBy(['name' => $name]);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        // Render the details template with the product data
        return $this->render('product/details.html.twig', [
            'productDetails' => $product,
        ]);
    }

    #[Route('/product/filter/{id}', name: 'product_filter')]
    public function filterByCat(ProductRepository $productRepository,
     $id, PaginatorInterface $paginator, Product $product, 
     Request $request,CategoryRepository $categoryRepository): Response
    {
        // $productsFilter = $productRepository->findByCategory($id);
        $productsFilter = $productRepository->findByCategory($id); 
            
        $categorys = $categoryRepository->findAll(); 
        
        // dd($categorys);
        return $this->render('product/prodFilter.html.twig', [
            'productsFilter' => $productsFilter,
            'categorys'=> $categorys,
        ]);
    }
}
