<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un objet Faker
        $faker = Factory::create('fr_FR');

        //Création entre 15 et 20 tâches aléatoirement

        for ($t = 0; $t < mt_rand(15, 30); $t++) {
            // Création d'un nouvel objet Task
            $task = new Task;

            // On nourrit l'objet Task 
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'));

            // On fait persister les données
            $manager->persist($task);
        }

        $manager->flush();
    }
}
