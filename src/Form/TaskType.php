<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Task;
use App\Service\Bartender;
use App\Service\CallApiService;
use App\Controller\TaskController;
use App\Service\BeerConnectionManager;
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

    public $tempData = 'temp';

    public function  __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $bartender = new Bartender();
        $filteredBeerListNameName = $bartender->filterBeerList();

        $builder
            ->add('name', ChoiceType::class, [
                'choices' => $filteredBeerListNameName,
                'label' => $this->translator->trans('general.name')
            ])

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
