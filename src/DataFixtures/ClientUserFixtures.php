<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class ClientUserFixtures extends Fixture
{

    /** @var Generator */
    protected $faker;

    private static $name = [
        'Orange',
        'SFR',
        'Virgin',
        'Bouygues'
    ];

    public function load(ObjectManager $manager)
    {

        $this->faker = Factory::create('fr_FR');

        //create clients
        for ($i = 0 ; $i <= 3; $i++) {

            $client = new Client();
            $client->setName(self::$name[$i]);

            $manager->persist($client);
            $clients[] = $client;
        }

        //create users
        for ($i = 0 ; $i <= 10 ; $i++) {

            $user = new User();
            $user->setUsername($this->faker->userName)
                ->setPassword($this->faker->password)
                ->setEmail($this->faker->email)
                ->setClient($this->faker->randomElement($clients));

            $manager->persist($user);
        }

        $manager->flush();
    }


}
