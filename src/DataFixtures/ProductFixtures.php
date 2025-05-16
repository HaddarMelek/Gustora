<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Classic American Breakfast Platter',
                'description' => 'A generous platter of fluffy mini pancakes served with Nutella, scrambled eggs with herbs, crispy bacon, and assorted sides including blueberries, strawberries, marshmallows, and maple syrup.',
                'price' => 42.00,
                'image' => '1.jpg',
                'category_id' => 1,
                'stock' => 10
            ],
            [
                'name' => 'Gourmet Brunch Feast',
                'description' => 'An elegant brunch spread featuring Belgian waffles, fresh berries, yogurt with honey, granola, mango slices, bagels, croissants, grilled cheese, smoked salmon, baked beans, fresh salad, and orange juice.',
                'price' => 56.00,
                'image' => '2.jpg',
               'category_id' => 1,
                'stock' => 20
            ],
            [
                'name' => 'Healthy Morning Boost',
                'description' => 'A nutritious bowl of Greek yogurt topped with banana, strawberries, blueberries, granola, and chia seeds. Served with avocado toast topped with cherry tomatoes and a glass of fresh orange juice.',
                'price' => 30.00,
                'image' => '3.jpg',
                'category_id' => 1,
                'stock' => 15
            ],
            [
                'name' => 'Garden Brunch Feast',
                'description' => 'A vibrant spread of smashed avocado toast topped with cherry tomatoes and feta, buttery croissants, chocolate spread, macarons, scrambled eggs, fresh fruits (bananas, strawberries, raspberries), smoked salmon, and homemade waffles. Served with tropical juice and herbal tea.',
                'price' => 75.00,
                'image' => '4.jpg',
               'category_id' => 1,
                'stock' => 8
            ],
            [
                'name' => 'Avocado Bloom Brunch',
                'description' => 'Artfully plated avocado toast with elegant avocado roses, alfalfa sprouts, and cherry tomatoes, paired with sunny-side-up eggs on multigrain bread. Served with velvety cappuccinos for a touch of café charm.',
                'price' => 53.00,
                'image' => '5.jpg',
               'category_id' => 1,
                'stock' => 5,
            ],
            [
                'name' => 'Deluxe Cozy Morning',
                'description' => 'A rich variety featuring avocado toast with berries and seeds, warm pancakes and eggs with smoked salmon, fresh waffles topped with peaches and whipped cream, granola fruit bowl, mini pastries, and a slice of layered cake. Accompanied by hot cocoa and spiced lattes.',
                'price' => 84.00,
                'image' => '6.jpg',
               'category_id' => 1,
                'stock' => 22
            ],
            [
                'name' => 'Brunch Deluxe',
                'description' => 'A fresh, modern brunch plate featuring a poppy seed bagel with cream cheese and cucumber ribbons, tropical fruit pancakes, a smoothie bowl topped with granola, berries, mango, kiwi, and honey. Includes your choice of a latte, iced coffee, or pink beetroot latte.',
                'price' => 54.50,
                'image' => '7.jpg',
               'category_id' => 1,
                'stock' => 25
            ],
            [
                'name' => 'Parisian Classic',
                'description' => 'A traditional French breakfast with a buttery croissant, a half baguette with butter, and artisanal fruit jam. Served with café crème and a glass of freshly squeezed orange juice.',
                'price' => 36.50,
                'image' => '8.jpg',
                'category_id' => 1,
                'stock' => 10
            ],
            [
                'name' => 'Pancake Sharing Board',
                'description' => 'A generous board of fluffy pancakes topped with powdered sugar and served with assorted toppings: chocolate spread, peanut butter, butter, jam, maple syrup, bananas, strawberries, and blueberries.',
                'price' => 66.50,
                'image' => '9.jpg',
              'category_id' => 1,
                'stock' => 7
            ],
            [
                'name' => 'Vibrant Morning',
                'description' => 'Two slices of artisanal toast topped with avocado mash, cherry tomatoes, pomegranate seeds, Fluffy mini pancakes served with fresh raspberries, blueberries, and a drizzle of syrup.Choice of a creamy oat milk latte or cappuccino, paired with fresh orange or pink grapefruit juice.A thick smoothie bowl topped with banana slices, raspberries, coconut flakes, granola, and nuts. Choose between açai or mango base.',
                'price' => 85.00,
                'image' => '10.jpg',
               'category_id' => 1,
                'stock' => 6
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setImage($productData['image']);
            $product->setStock($productData['stock']);
        
            // Find Category entity by id
            $category = $this->getReference('category_' . $productData['category_name']);
    $product->setCategory($category);

    $manager->persist($product);   }
        
        $manager->flush();
    }
}