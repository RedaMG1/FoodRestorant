<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminToolsController extends AbstractController
{
    #[Route('/admin/tools', name: 'app_admin_tools')]
    public function index(): Response
    {
        return $this->render('admin_tools/index.html.twig', [
            'controller_name' => 'AdminToolsController',
        ]);
    }
}
