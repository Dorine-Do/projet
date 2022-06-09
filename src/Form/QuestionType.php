<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Question;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class QuestionType extends AbstractType
{
    // private $moduleRepository;
    // public function __construct(ModuleRepository $moduleRepository)
    // {
    //     $this->moduleRepository = $moduleRepository;
    // }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            // ->add('id_author')
            ->add('wording',TextareaType::class,[
                'label'=>false,
            ])
            ->add('difficulty', ChoiceType::class,[

                'choices' => [
                    'Difficulté' => [

                        'Facile' => 1,
                        'Moyen' => 2,
                        'Difficile' => 3,

                    ],
                ]
            ])
            ->add('proposal', CollectionType::class, [
                'entry_type' => ProposalFormType::class,
                'label' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
//            ->add('response_type')
//           ->add('is_official')
            ->add('module', EntityType::class, [
                'class'=> Module::class,
                'label'=>false,

            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}