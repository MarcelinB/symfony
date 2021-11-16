<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/task/create", name="task_create")
     */
    public function creatTask(Request $request)
    {
        $task = new Task;
        $form = $this->createForm(TaskType::class, $task, []);

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
