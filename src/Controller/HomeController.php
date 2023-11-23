<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(PaginatorInterface $paginator, 
    CategoryRepository $categoryRepository,ProductRepository $productRepository
    ,Request $request): Response
    {
        $categories = $paginator->paginate(
            $categoryRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $products = $paginator->paginate(
            $productRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        return $this->render('home/index.html.twig', [
            'categorys' => $categories,
            'products'=>$products,
        ]);
    }
}
