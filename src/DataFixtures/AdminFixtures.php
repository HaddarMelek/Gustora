<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roleAdmin = new Role();
        $roleAdmin->setRole('ROLE_ADMIN');
        $manager->persist($roleAdmin);

        $roleUser = new Role();
        $roleUser->setRole('ROLE_USER');
        $manager->persist($roleUser);

        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setCountryCode('US');
        $adminUser->setPhoneNumber('1234567890');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'adminpassword'));

        $adminUser->addRole($roleAdmin); 
        $manager->persist($adminUser);

        $manager->flush();
    }
}
