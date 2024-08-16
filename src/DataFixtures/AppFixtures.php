<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher) 
    {
        $this->passwordHasher  = $passwordHasher ;
    }

    public function load(ObjectManager $manager): void
    {
        $Virgil = new User($this->passwordHasher);
        $Virgil->setUsername("Virgil")->setPassword("123");
        $manager->persist($Virgil);
        $jonathan = new User($this->passwordHasher);
        $jonathan->setUsername("jonathan")->setPassword("123456");
        $manager->persist($jonathan);

        $manager->flush();
    }
}
