<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\AddressType;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccountController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/account', name: 'app_account')]
    public function index(
        UserRepository $userRepository,
        Security $security,
        AddressRepository $addressRepository
    ): Response {
        $user = $security->getUser();
        if(!$user){
            return $this->redirectToRoute('login');
        }
        $addressInfo = $addressRepository->findOneBy([
            'user' => $user
        ]);
        
        return $this->render('account/index.html.twig', [
            'addressInfo' => $addressInfo,
            'user' => $user,
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/edit/{id}', name: 'edit_account', methods: ['GET', 'POST'])]
    public function editAccount(
        UserRepository $reposetory,
        AuthorizationCheckerInterface $authorizationChecker,
        int $id,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $user = $reposetory->findOneBy(['id' => $id]);
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        $currentDate = date('Y-m-d H:i:s');

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setUpdatedAt($currentDate);
            $user = $form->getData();
            $manager->persist($user);
            $manager->flush();
            // dd($form->getData($user));
            $this->addFlash('success', 'User edited successfully!');
            return $this->redirectToRoute('account');
        }
        return $this->render('account/edit.html.twig', [

            'button' => 'Submit',
            'form' => $form->createView(),
            'user'=>$user,
        ]);
    }

    #[Route('/account/address/edit/{id}', name: 'edit_address', methods: ['GET', 'POST'])]
    public function editAddress(
        Security $security,UserRepository $userRepository,
        int $id,AddressRepository $addressRepository,
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $user = $userRepository->findOneBy(['id' => $id]);
        $existingAddress = $addressRepository->findOneBy([
            'user'=>$user,
        ]);
        if(!$existingAddress){
            $address = new Address();
        }else{
            $address = $existingAddress;
        }
       
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $currentDate = date('Y-m-d H:i:s');

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($user);
            $address->setUpdatedAt($currentDate);
            $address = $form->getData();
            
            $manager->persist($address);
            $manager->flush();
        
            $this->addFlash('success', 'address edited successfully!');
            return $this->redirectToRoute('account');
        }
        return $this->render('account/address.html.twig', [
            'button' => 'Submit',
            'form' => $form->createView(),
        ]);
    }
}
