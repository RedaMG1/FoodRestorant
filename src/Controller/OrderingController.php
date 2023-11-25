<?php

namespace App\Controller;

use App\Entity\Ordering;
use App\Repository\CartRepository;
use App\Repository\OrderingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderingController extends AbstractController
{
    #[Route('/ordering', name: 'app_ordering')]
    public function index(): Response
    {
        return $this->render('ordering/index.html.twig', [
            'controller_name' => 'OrderingController',
        ]);
    }

    
}
