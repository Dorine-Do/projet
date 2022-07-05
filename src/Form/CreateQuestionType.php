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
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class CreateQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wording',TextareaType::class,[
                'required' => true,
            ])
            ->add('difficulty', enumType::class,[
                "class" => Difficulty::class,
                'choice_label'=> 'value',
                'expanded' => true,
            ])

            // Imbriquation de formulaire voir instructor > index.html.twig
            ->add('proposals', CollectionType::class, [
                'entry_type' => ProposalFormType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 2,
                        'max' => 6,
                        'minMessage' => 'La question doit contenir au moins deux réponses',
                        'maxMessage' => 'You cannot specify more than {{ limit }} emails',
                    ]),
                    new Assert\Callback(
                        ['callback' => static function ( $data, ExecutionContextInterface $context) {
//                            dd($data);
//
                            foreach($data as $p){
                                if($p->getIsCorrect() == true){
                                    return;
                                }
                            }
                            $context->getRoot()->addError(new FormError('La question doit contenir au moins une bonne réponse'));
                        }]
                    )
                ]
            ])

            //    Intégration d'une autre entité dans un form
            ->add('module', EntityType::class, [
                'class'=> Module::class,

            ])

            ->add('enabled', CheckboxType::class, [
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
