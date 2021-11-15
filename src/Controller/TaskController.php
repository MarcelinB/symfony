<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    /**
     * @Route("/task/listing", name="task")
     */
    public function index(): Response
    {
        // On va chercher avec Doctrine le Repository de nos task

        $repository = $this->getDoctrine()->getRepository(Task::class);

        // dans ce repository nous recuperons toutes les données
        $tasks = $repository->findAll();

        // Affichages des donnes dans le var_dumper 
        //dd($tasks);


        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
