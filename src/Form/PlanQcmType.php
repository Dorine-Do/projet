<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PlanQcmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // TODO: recupérer uniquement les session liées au formateur
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choices' => function() {

                }
            ])
            // TODO: recupérer les modules liés à cette session (dynamique)
            ->add('module')
            // TODO: recupérer les QCM liés à ce module (dynamique)
            ->add('qcm')
            // TODO: recupérer les Eleves liés à cette session (dynamique)
            ->add('student')
            ->add('startTime')
            ->add('endTime')
        ;
    }
}
