<?php


namespace App\Controller;

use Dompdf\Dompdf;
use App\Service\CallApiService;
use Dompdf\Options;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\Bartender;
use App\Service\BeerConnectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TaskController extends AbstractController
{
    /**
     * @beerList
     */
    // public static $beerList = [];

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
        $role = $user->getRoles();
        $id = $user->getId();
        $admin = 'ROLE_ADMIN';
        $slug = $user->getIsPrefered();
        //dd($user);
        if (in_array($admin, $role)) {
            $tasks = $this->repository->findBy(array('isArchived' => '0'));
        } else {
            $tasks = $this->repository->findBy(array(
                'isArchived' => '0',
                'user' => $id
            ));
        }
        // Dans le repo, on récupère les entrées
        //$tasks = $this->repository->findAll();

        // Affichage dans le var_dumper
        // dd($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/task/archives", name="task_archives")
     */
    public function indexArchives()
    {
        //récuperer les infos de l'utilisateur connecté 
        $user = $this->getUser();
        $role = $user->getRoles();
        $id = $user->getId();
        $admin = 'ROLE_ADMIN';
        //dd($user);
        if (in_array($admin, $role)) {
            $tasks = $this->repository->findBy(array('isArchived' => '1'));
        } else {
            $tasks = $this->repository->findBy(array(
                'isArchived' => '1',
                'user' => $id
            ));
        }
        // Dans le repo, on récupère les entrées
        //$tasks = $this->repository->findAll();

        // Affichage dans le var_dumper
        // dd($tasks);

        return $this->render('task/archives.html.twig', [
            'tasks' => $tasks
        ]);
    }


    /** 
     *@Route("/task/create", name="task_create")
     *@Route("/task/update/{id}", name="task_update", requirements={"id"="\d+"})
     */

    public function task(Task $task = null, Request $request)
    {
        //$cc = new BeerConnectionManager(CallApiService $callApiService);
        //$cc->test();
        $tasktype = new Bartender();
        $beerList = $tasktype->filterBeerList();

        if (!$task) {
            $task = new Task;

            $task->setCreatedAt(new \DateTime());

            $user = $this->getUser();
            $task->setUser($user);
        }

        $this->addFlash(
            'success',
            'L\'action a bien été effectuée'
        );

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
            //ADD le tablo
            //dd($beerList),

            //dd le tablo
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

        $this->addFlash(
            'success',
            'La suppression a bien été effectuée'
        );

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

    /**
     * @Route("/task/create", name="OK")
     */
    /*public function viewApi(CallApiService $callApiService)
    {
        $bartender = new Bartender();
        $beerList = $bartender->filterBeers($callApiService->getBeerTitle());
        return $beerList;
    }*/

    // Vérification si la date de la tache est passée.
    public function checkDueAt(Task $task)
    {
        $flag = false;
        $dueAt = $task->getDueAt();
        $today = new \DateTime();

        if ($today > $dueAt) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * @Route("/task/archive/{id}", name="task_archive", requirements={"id"="\d+"})
     * @return Response
     */
    public function archiveTask(Task $task)
    {
        if ($this->checkDueAt($task)) {
            $task->setIsArchived(1);
            $this->manager->persist($task);
            $this->manager->flush();
            $this->addFlash(
                'succes',
                'La tâche a bien été archivée'
            );
        } else {
            $this->addFlash(
                'warning',
                'Impossible d\'archiver une tâche dont l\'échéance n\'a pas eu lieu'
            );
        }
        return $this->redirectToRoute("task_listing");
    }

    /**
     * @Route("/task/archives_{slug}")
     *
     * @param String $slug
     * @return void
     */
    public function displayTable(String $slug)
    {


        //  Récupération des infos de l'utilisateur.
        $user = $this->getUser();


        if ($slug != 'manual') {
            $tasks = $this->repository->findAll();
            $user->setIsPrefered(0);
            for ($i = 0; $i < count($tasks); $i++) {
                if ($this->checkDueAt($tasks[$i])) {
                    $this->archiveTask($tasks[$i]);
                }
            }
        } else {
            $user->setIsPrefered(1);
        }
        $this->manager->persist($user);
        $this->manager->flush();

        return $this->index();
    }
}
