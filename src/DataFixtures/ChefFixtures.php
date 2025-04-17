<?php

namespace App\DataFixtures;

use App\Entity\Chef;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChefFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $chefs = [
            [
                'name' => 'Ahmed Al-Mansouri',
                'position' => 'Executive Chef',
                'image' => 'team-1.jpg'
            ],
            [
                'name' => 'Karim Ben Hassan',
                'position' => 'Head Chef',
                'image' => 'team-2.jpg'
            ],
            [
                'name' => 'Aymen Zouari',
                'position' => 'Pastry Chef',
                'image' => 'team-3.jpg'
            ],
            [
                'name' => 'Youssef El-Amiri',
                'position' => 'Sous Chef',
                'image' => 'team-4.jpg'
            ]
        ];

        foreach ($chefs as $chefData) {
            $chef = new Chef();
            $chef->setName($chefData['name'])
                 ->setPosition($chefData['position'])
                 ->setImage($chefData['image']);
            $manager->persist($chef);
        }

        $manager->flush();
    }
}