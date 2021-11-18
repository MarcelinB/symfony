<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Task;
use App\Service\Bartender;
use App\Service\CallApiService;
use App\Controller\TaskController;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public $beerList = [];

    public function  __construct(CallApiService $callApiService)
    {
        $bartender = new Bartender();
        $this->beerList = $bartender->filterBeers($callApiService->getBeerTitle());
    }
    public function filterBeerList()
    {
        $FilteredBeerNameList = [];
        //dd($this->beerList);

        for ($i = 0; $i < count($this->beerList); $i++) {

            array_push($FilteredBeerNameList, [$this->beerList[$i]->getName() => $this->beerList[$i]->getName()]);
        }

        //dd($FilteredBeerNameList);
        return $FilteredBeerNameList;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        //$FilteredBeerNameList = $this->filterBeerList();

        $builder
            ->add('name', ChoiceType::class, [
                'choices' => $this->filterBeerList(),
                'label' => 'Nom de la tâche'
            ])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('dueAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date effective'
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn-danger'
                ]
            ]);

        dd($builder->get('name')->getAttributes()['data_collector/passed_options']['choices'][0]);
    }
    //->getViewTransformers()[0]


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
