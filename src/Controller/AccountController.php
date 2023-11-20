<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(UserRepository $userRepository, Security $security,
    AddressRepository $addressRepository): Response
    {
        $user = $security->getUser();
        $addressInfo = $addressRepository->findOneBy([
            'user'=>$user
        ]);
        return $this->render('account/index.html.twig', [
            'addressInfo'=>$addressInfo,
            'user'=>$user,
            'controller_name' => 'AccountController',
        ]);
    }
}
