<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $paginator->paginate(
            $categoryRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // ----------------------------------------------------admin----------------------

    #[Route('/category/admin', name: 'app_category')]
    public function adminIndex(
        
        PaginatorInterface $paginator,
        Request $request,
        CategoryRepository $categoryRepository
    ): Response {
        
        $categorys = $paginator->paginate(
            $categoryRepository->findAll(), // query
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('category/admin/adminCategory.html.twig', [
            
            'categorys' => $categorys,
        ]);
    }

    #[Route('/category/create', name: 'create_category')]
    public function adminCategoryCreate(
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $manager,
  
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            
            $manager->persist($category);
            $manager->flush();
            // dd($form->getData($ingerdient));
            $this->addFlash('success', 'Category is created successfully!');
            return $this->redirectToRoute('adminCategory');
        }
        return $this->render('category/admin/create.html.twig', [
            
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/edit/{id}', name: 'edit_category', methods: ['GET', 'POST'])]
    public function adminCategoryEdit(
        CategoryRepository $categoryRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $category = $categoryRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $currentDate = date('Y-m-d H:i:s');
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            
            $manager->persist($category);
            $manager->flush();
            
            $this->addFlash('success', 'Category edited successfully!');
            return $this->redirectToRoute('adminCategory');
        }
        return $this->render('category/admin/edit.html.twig', [

            'button' => 'Submit',
            'form' => $form->createView(),

        ]);
    }
    #[Route('/category/delete/{id}', name: 'delete_category', methods: ['GET', 'POST'])]
    public function delete(
        int $id,
        Request $request,
        EntityManagerInterface $manager,
        Category $category,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
            return $this->render('access_denied.html.twig');
        }
        $manager->remove($category);
        $manager->flush();

        $this->addFlash('success', 'category deleted successfully!');
        return $this->redirectToRoute('adminCategory');
    }
}
