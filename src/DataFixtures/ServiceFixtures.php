<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = [
            [
                'icon' => 'fa-user-tie',
                'title' => 'Expert Chefs',
                'description' => 'Our team of expert chefs brings years of culinary excellence to create exceptional dining experiences.',
                'delay' => '0.1'
            ],
            [
                'icon' => 'fa-utensils',
                'title' => 'Quality Cuisine',
                'description' => 'We serve authentic Tunisian and Mediterranean dishes made with the finest local ingredients.',
                'delay' => '0.3'
            ],
            [
                'icon' => 'fa-cart-plus',
                'title' => 'Online Ordering',
                'description' => 'Enjoy our delicious meals from the comfort of your home with our convenient online ordering system.',
                'delay' => '0.5'
            ],
            [
                'icon' => 'fa-headset',
                'title' => 'Customer Service',
                'description' => 'Our dedicated team ensures every guest receives exceptional service and attention to detail.',
                'delay' => '0.7'
            ],
        ];

        foreach ($services as $serviceData) {
            $service = new Service();
            $service->setIcon($serviceData['icon'])
                   ->setTitle($serviceData['title'])
                   ->setDescription($serviceData['description'])
                   ->setDelay($serviceData['delay']);
            $manager->persist($service);
        }

        $manager->flush();
    }
}