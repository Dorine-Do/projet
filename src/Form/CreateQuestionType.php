<?php

namespace App\Form;


use App\Entity\Enum\Difficulty;
use App\Entity\Main\Module;
use App\Entity\Main\Question;
use App\Entity\Main\User;
use App\Form\EventListener\AddUserData;
use App\Repository\ModuleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class CreateQuestionType extends AbstractType
{
    const DIFFICULTIES = [
        1 => 'Facile',
        2 => 'Moyen',
        3 => 'Difficile',
    ];

    private $security;
    private $manager;
    private $user;


    public function __construct( Security $security ,EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->manager=$manager;
        $this->user = $userRepository->find(1);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wording',CKEditorType::class,[
                'config' => [
                    'uiColor' => '#FFAC8F',
                    'toolbar' => [
                        [
                            'Bold',
                            'Italic',
                            'Underline',
                            'JustifyLeft',
                            'JustifyCenter',
                            'JustifyRight',
                            'JustifyBlock',
                            'CodeSnippet',
                            'Blockquote',
                            'Indent',
                            'Outdent',
                            'Image'
                        ]
                    ],
                    'extraPlugins' => ['codesnippet'],
                    'codeSnippet_theme' => 'monokai'
                ],
            ])
            ->add('difficulty', enumType::class,[
                "class" => Difficulty::class,
                'choice_label'=> static function (\UnitEnum $choice): string
                    {
                        return self::DIFFICULTIES[$choice->value];
                    },
                'expanded' => true,
                'empty_data' => self::DIFFICULTIES[2],
                "label"=>false
            ])

            // Imbriquation de formulaire
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
                        'maxMessage' => 'La question doit contenir au maximum six réponses',
                    ]),
                    new Assert\Callback(
                        ['callback' => static function ( $data, ExecutionContextInterface $context) {
                            foreach($data as $p){
                                if($p->getIsCorrectAnswer() == true){
                                    return;
                                }
                            }
                            $context->getRoot()->addError(new FormError('Pour valider le formulaire, veuillez cocher au moins une réponse correcte!'));
                        }]
                    )
                ]
            ])

            //    Intégration d'une autre entité dans un form
            ->add('module', EntityType::class, [
                'class'=> Module::class,
                'choice_label'=>'title',
                'empty_data' =>function(){
                   $module= new Module();
                   $module->setTitle('Choix du module');
                   $this->manager->persist($module);
                   return $module;
                },
            ])

            ->add('explanation',CKEditorType::class,[
                'config' => [
                    'uiColor' => '#FFAC8F',
                    'toolbar' => [
                        [
                            'Bold',
                            'Italic',
                            'Underline',
                            'JustifyLeft',
                            'JustifyCenter',
                            'JustifyRight',
                            'JustifyBlock',
                            'CodeSnippet',
                            'Blockquote',
                            'Indent',
                            'Outdent',
                            'Image'
                        ]
                    ],
                    'extraPlugins' => ['codesnippet'],
                    'codeSnippet_theme' => 'monokai'
                ],
            ])

            ->add('is_enabled', CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
        ;



        //if (in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())){
        /* TODO A enlever quand la connexion via google sera opperationelle */
        if (in_array('ROLE_ADMIN', $this->user->getRoles())){
            $builder
                    ->add('is_mandatory', CheckboxType::class, [
                        'required' => false,
                        'label' => false,
                    ]);
        }
        //if (
        //  in_array('ROLE_ADMIN', $this->security->getUser()->getRoles()) ||
        //  $this->security->getUser()->isReferent &&
        //  in_array('ROLE_INSTRUCTOR', $this->security->getUser()->getRoles())
        //){
        /* TODO A enlever quand la connexion via google sera opperationelle */
        if (
            $this->user->isReferent() &&
            in_array('ROLE_INSTRUCTOR', $this->user->getRoles()) ||
            in_array('ROLE_ADMIN', $this->user->getRoles())
        )
        {
            $builder
                ->add('is_official', CheckboxType::class, [
                'required' => false,
                'label' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }

}
