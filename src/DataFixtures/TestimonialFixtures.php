<?php

namespace App\DataFixtures;

use App\Entity\Testimonial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestimonialFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $testimonials = [
            [
                'name' => 'Nadia Ben Ali',
                'profession' => 'Food Critic',
                'content' => 'An exceptional dining experience at Gustora! The blend of traditional Tunisian flavors with modern culinary techniques created unforgettable dishes. The service was impeccable.',
                'image' => 'testimonial-1.jpg'
            ],
            [
                'name' => 'Sami Mansour',
                'profession' => 'Food Blogger',
                'content' => 'The attention to detail in every dish is remarkable. From the welcoming atmosphere to the innovative menu, Gustora delivers an authentic taste of Tunisia with a contemporary twist.',
                'image' => 'testimonial-2.jpg'
            ],
            [
                'name' => 'Kamel Hamdi',
                'profession' => 'Regular Customer',
                'content' => 'As a regular patron, I can confidently say that Gustora maintains consistently high standards. Their couscous and grilled specialties are simply outstanding.',
                'image' => 'testimonial-3.jpg'
            ],
            [
                'name' => 'Leila Ben Salah',
                'profession' => 'Local Business Owner',
                'content' => 'Gustora has become our go-to place for business dinners. The ambiance, service, and exceptional food make it perfect for both casual dining and special occasions.',
                'image' => 'testimonial-4.jpg'
            ]
        ];

        foreach ($testimonials as $testimonialData) {
            $testimonial = new Testimonial();
            $testimonial->setName($testimonialData['name'])
                       ->setProfession($testimonialData['profession'])
                       ->setContent($testimonialData['content'])
                       ->setImage($testimonialData['image']);
            $manager->persist($testimonial);
        }

        $manager->flush();
    }
}