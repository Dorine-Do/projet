<?php

namespace App\Form;

use App\Entity\Enum\Difficulty;
use App\Entity\Module;
use App\Entity\Question;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

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
                'constraints' => [
                    new Assert\Length([
                        'min' => 0,
                        'minMessage' => "La question ne peut pas être vide.",
                        'max' => 250,
                        'maxMessage' => "La question doit faire moins de 250 caractères.",
                    ]),
                ]
            ])
            ->add('difficulty', enumType::class,[
                "class" => Difficulty::class,
                'choice_label'=> 'value',
                'label'=>false,

            ])
            ->add('proposal', CollectionType::class, [
                'entry_type' => ProposalFormType::class,
                'label' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])



            ->add('module', EntityType::class, [
                'class'=> Module::class,
                'label'=>false,

            ])

            ->add('enabled', CheckboxType::class, [
            'label'    => 'Hors service',
            'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}