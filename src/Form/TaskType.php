<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Task;
use App\Service\Bartender;
use App\Service\CallApiService;
use App\Controller\TaskController;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskType extends AbstractType
{
    /**
     * 
     *
     * @var TranslatorInterface
     */
    private $translator;


    public $beerList = [];
    public $tempData = 'temp';

    public function  __construct(CallApiService $callApiService, TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
                'label' => $this->translator->trans('general.name')
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $product = $event->getData();
                $form = $event->getForm();
                var_dump($event->getData());
                $this->tempData = $event;
                // checks if the Product object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "Product"
                if (!$product /*|| null === $product->getId()*/) {
                    $form->add('name', ChoiceType::class, [
                        'choices' => $this->filterBeerList(),
                        'label' =>  $this->translator->trans('general.name')
                    ]);
                }
            })

            ->add('description', TextareaType::class, ['label' => $this->translator->trans('general.description')])
            ->add('dueAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' =>  $this->translator->trans('general.due_date')
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->translator->trans('general.button.success'),
                'attr' => [
                    'class' => 'btn-danger'
                ]
            ]);
        //dd($this->tempData);
        //dd($builder->get('name')->getAttributes()['data_collector/passed_options']['choices'][0]);
    }
    //->getViewTransformers()[0]


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
