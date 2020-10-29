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
        'iPhone 11 Pro',
        'iPhone 11 Pro Max',
        'iPhone 12',
        'iPhone 12 Mini',
        'iPhone 12 Pro',
        'Galaxy S10 Lite',
        'Galaxy S10e',
        'Galaxy S10+',
        'Galaxy S20 FE',
        'Galaxy S20',
        'Galaxy S20+',
        'Galaxy S20 Ultra',
        'Galaxy Note 20',
        'Huawei P30',
        'Huawei P30 Pro',
        'Huawei Mate 30',
        'Huawei Mate 30 Pro',
        'Huawei P40',
        'Huawei P40 Pro',
        'Huawei mate 40',
        'Huawei mate 40 Pro',

    ];

    private static $brand = [
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Apple',
        'Samsung',
        'Samsung',
        'Samsung',
        'Samsung',
        'Samsung',
        'Samsung',
        'Samsung',
        'Samsung',
        'Huawei',
        'Huawei',
        'Huawei',
        'Huawei',
        'Huawei',
        'Huawei',
        'Huawei',
        'Huawei'
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
        for ($i = 0; $i <= 24; $i++) {

             $product = new Product();
             $product->setBrand(self::$brand[$i])
                 ->setModel(self::$model[$i])
                 ->setPrice(self::$price[array_rand(self::$price)]);

             $manager->persist($product);

        }

        $manager->flush();
    }
}
