<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un objet Faker
        $faker = Factory::create('fr_FR');

        //Création de nos 5 catégories
        for ($c = 0; $c < 5; $c++) {
            //création d'un nouvel objet tag 
            $tag = new Tag;

            //On ajoute un nom à notre catégorie
            $tag->setName($faker->colorName());

            //On fait persister les données
            $manager->persist($tag);
        }

        // On push les catégories en BDD
        $manager->flush();

        //récupération des catégories créées
        $tags = $manager->getRepository(Tag::class)->findAll();

        //Création entre 15 et 20 tâches aléatoirement
        for ($t = 0; $t < mt_rand(15, 30); $t++) {
            // Création d'un nouvel objet Task
            $task = new Task;

            // On nourrit l'objet Task 
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'))
                ->setTag($faker->randomElement($tags));

            // On fait persister les données
            $manager->persist($task);
        }


        $manager->flush();
    }
}
