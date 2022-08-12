<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\QcmPlanner;
use App\Entity\Session;
use App\Repository\SessionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PlanQcmType extends AbstractType
{
    private $security;

    public function __construct( Security $security )
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction( '/instructor/plan_qcm' )
            ->add('session', EntityType::class, [
            'class'     => Session::class,
            'choice_label' => 'name',
            'query_builder'   => function( SessionRepository $sessionRepo ) {
                return $sessionRepo->getInstructorSessions( $this->security->getUser() );
            },
            'compound' => true,
            'multiple' => false,
        ]);

        $formModifier = function( FormInterface $form, Session $session = null ) {
            $linksSessionModule = $session === null ? [] : $session->getLinksSessionModule();
            $modules = [];
            foreach( $linksSessionModule as $linkSessionModule )
            {
                $modules[] = $linkSessionModule->getModule();
            }
            $form->add('module', EntityType::class, [
                'class' => Module::class,
                'choices' => $modules
            ]);
        };

        $builder->addEventListener( FormEvents::POST_SET_DATA , function(FormEvent $event) use ( $formModifier ) {
//            dd( $event->getForm()->getData() );
            $formModifier( $event->getForm(), $event->getData() );
//            $formModifier( $event->getForm(), $event->getData()->getSession() );
        });

        $builder->get('session')->addEventListener( FormEvents::POST_SUBMIT, function( FormEvent $event ) use ( $formModifier ) {
            $session = $event->getForm()->getData();
            $formModifier( $event->getForm()->getParent(), $session );
        });

//            // TODO: recupérer les QCM liés à ce module (dynamique)
//            ->add('qcm', EntityType::class, [
//                'class'     => Qcm::class,
//                'choices'   => function( Security $security ) {
//                    $instructor = $security->getUser();
//
//                }
//            ])
//            // TODO: recupérer les Eleves liés à cette session (dynamique)
//            ->add('students', EntityType::class, [
//                'class'     => Student::class,
//                'choices'   => function() {
//
//                }
//            ])
//            ->add('startTime', DateType::class)
//            ->add('endTime', DateType::class);
    }

//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => QcmPlanner::class,
//        ]);
//    }
}
