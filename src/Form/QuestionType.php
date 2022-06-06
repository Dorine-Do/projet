<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Question;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_author')
            ->add('wording')
            ->add('is_mandatory')
            ->add('is_official')
            ->add('difficulty')
            ->add('response_type')
            ->add('created_at')
            ->add('updated_at')
            ->add('enabled')
            ->add('module_id', EntityType::class, [
                'class' => Module::class
            ]);
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}

