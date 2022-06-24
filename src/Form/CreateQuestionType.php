<?php

namespace App\Form;


use App\Entity\Enum\Difficulty;
use App\Entity\Module;
use App\Entity\Question;
use Doctrine\Common\Annotations\Annotation\Enum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class CreateQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wording',TextareaType::class,[
                'label'=>false,
                'required' => true,
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

            // Imbriquation de formulaire voir instructor > index.html.twig
            ->add('proposal', CollectionType::class, [
                'entry_type' => ProposalFormType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])


            //    Intégration d'une autre entité dans un form
            ->add('module', EntityType::class, [
                'class'=> Module::class,
//                'class'=> Module::getTitle(),
                'label'=>false,

            ])

            ->add('enabled', CheckboxType::class, [
                'label'    => false,
                'required' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
