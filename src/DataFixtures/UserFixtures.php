<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $email = "admin@exemple.com";
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
        if(!$existingUser){
        $currentDate = date('Y-m-d H:i:s');

        $user->setEmail($email);
        $user->setFirstName("Jimmy");
        $user->setLastName("MG");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setCreatedAt($currentDate);
        $user->setUpdatedAt($currentDate);

        $manager->persist($user);
        $manager->flush();}
    }
}
