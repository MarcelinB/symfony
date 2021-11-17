<?php


namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;


class TaskController extends AbstractController
{
    /** 
     * @var TaskRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(TaskRepository $repository, EntityManagerInterface $manager)
    {

        $this->repository = $repository;
        $this->manager = $manager;
    }
    /**
     *@Route("/task/listing", name="task_listing")
     */
    public function index(): Response
    {
        //récuperer les infos de l'utilisateur connecté 
        $user = $this->getUser();
        //dd($user);

        // Dans le repo, on récupère les entrées
        $tasks = $this->repository->findAll();

        // Affichage dans le var_dumper
        // dd($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /** 
     *@Route("/task/create", name="task_create")
     *@Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */

    public function task(Task $task = null, Request $request)
    {
        if (!$task) {
            $task = new Task;

            $task->setCreatedAt(new \DateTime());
        }


        $form = $this->createForm(TaskType::class, $task, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas nécessaire
            // $task->setName($form['name']->getData())
            //     ->setDescription($form['description']->getData())
            //     ->setDueAt($form['dueAt']->getData())
            //     ->setTag($form['tag']->getData());

            // $manager = $this->getDoctrine()->getManager();
            $this->manager->persist($task);
            $this->manager->flush();

            return $this->redirectToRoute('task_listing');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/task/delete/{id}", name="task_delete", requirements={"id"="\d+"})
     */
    public function deleteTask(Task $task): Response
    {
        $this->manager->remove($task);
        $this->manager->flush();

        return $this->redirectToRoute("task_listing");
    }

    /**
     * @Route("/task/listing/download", name="task_download")
     */
    public function downloadPdf()
    {
        $tasks = $this->repository->findAll();

        // Définition des options du pdf
        $pdfoption = new Options;

        //Police par default 
        $pdfoption->set('defaultFont', 'Arial');
        $pdfoption->setIsRemoteEnabled(true);

        // On instancie DOMDF
        $dompdf = new Dompdf($pdfoption);

        //On genére le html 
        $html = $this->renderView('task/pdfdownload.html.twig',  [
            'tasks' => $tasks,
        ]);

        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'landscape');
        $dompdf->render();

        //On génère un nom de fichier 

        $fichier = 'LE PDF OUAIS OUAIS OUAIS CA MARCHE';

        //Envoyer le pdf au navigateur
        $dompdf->stream($fichier, [
            'Attachement' => true
        ]);
        return new Response();
    }
}
