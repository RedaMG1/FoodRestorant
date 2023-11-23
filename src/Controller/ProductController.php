<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
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
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request,
        CategoryRepository $categoryRepository
    ): Response {
        $products =  $productRepository->findAll(); // query
            
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
            'user'=>$logedUser,
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
        $this->addFlash('success', $product->getName().' has been added to your cart successfully!');
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
    public function filterByCat(
        ProductRepository $productRepository,
        $id,
        PaginatorInterface $paginator,
        Product $product,
        Request $request,
        CategoryRepository $categoryRepository
    ): Response {
        // $productsFilter = $productRepository->findByCategory($id);
        $productsFilter = $productRepository->findByCategory($id);

        $categorys = $categoryRepository->findAll();

        // dd($categorys);
        return $this->render('product/prodFilter.html.twig', [
            'productsFilter' => $productsFilter,
            'categorys' => $categorys,
        ]);
    }
// ----------------------------------------------------admin----------------------

    #[Route('/product/admin', name: 'app_product')]
    public function adminIndex(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request,
        CategoryRepository $categoryRepository
    ): Response {
        $products = $paginator->paginate(
            $productRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $categorys =
            $categoryRepository->findAll();

        return $this->render('product/admin/adminProduct.html.twig', [
            'products' => $products,
            'categorys' => $categorys,
        ]);
    }

    #[Route('/product/create', name: 'create_product')]
    public function adminProductCreate(
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
        ProductRepository $productRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $currentDate = date('Y-m-d H:i:s');
        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt($currentDate);
            $manager->persist($product);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Product is created successfully!');
            return $this->redirectToRoute('adminProduct');
        }
        return $this->render('product/admin/create.html.twig', [
            
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/edit/{id}', name: 'edit_product', methods: ['GET', 'POST'])]
    public function adminProductEdit(
        ProductRepository $reposetory,
        AuthorizationCheckerInterface $authorizationChecker,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $product = $reposetory->findOneBy(['id' => $id]);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $currentDate = date('Y-m-d H:i:s');
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            
            $manager->persist($product);
            $manager->flush();
            // dd($form->getData($product));
            $this->addFlash('success', 'Product edited successfully!');
            return $this->redirectToRoute('adminProduct');
        }
        return $this->render('product/admin/edit.html.twig', [

            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/product/delete/{id}', name: 'delete_product', methods: ['GET', 'POST'])]
    public function delete(
        ProductRepository $reposetory,
        int $id,
        Request $request,
        EntityManagerInterface $manager,
        Product $product,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'product deleted successfully!');
        return $this->redirectToRoute('adminProduct');
    }
}
