<?php

namespace App\Form;

use App\Entity\Proposal;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProposalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wording',TextareaType::class, [
                'label'    => 'Intitulé de la réponse',
                'attr' => ['class' => 'wording'],
            ])
            ->add('is_correct_answer',CheckboxType::class, [
                'label'    => 'Reponse correcte',
                'required' => false,
                'attr' => ['class' => 'isCorrect'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proposal::class,
        ]);
    }
}
