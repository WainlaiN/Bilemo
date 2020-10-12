<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    private static $model = [
        'iPhone X',
        'iPhone SE',
        'iPhone 11',
        'iPhone XS Max',
        'Galaxy S20',
        'Galaxy S20+',
        'Galaxy Note 20'
    ];

    private static $brand = [
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Samsung',
        'Samsung',
        'Samsung',
    ];

    private static $price = [
        '999$',
        '1299$',
        '799$',
        '899$',
        '599$',
        '499$',
        '849$',
    ];

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i <= 6; $i++) {

             $product = new Product();
             $product->setBrand(self::$brand[$i])
                 ->setModel(self::$model[$i])
                 ->setPrice(self::$price[$i]);

             $manager->persist($product);

        }

        $manager->flush();
    }
}
