<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Question;
use App\Entity\Enum\Difficulty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

use Symfony\Component\Validator\Constraints as Assert;
class QuestionType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            
        ->add('wording',TextType::class,[
            'label'=>false,
            'constraints' => [
                /*TODO faire un fichier où on stock les regex*/
                new Regex('#^[a-zA-Z0-9]+$#'),
                new Assert\Length([
                    'min' => 0,
                    'minMessage' => "La question ne peut pas être vide.",
                    'max' => 120,
                    'maxMessage' => "La question doit faire moins de 120 caractères.",
                ]),
            ]
        ])
            ->add('difficulty',EnumType::class,[
                'class'=>Difficulty::class,
                'choice_label' => static function (\UnitEnum $choice): string {
                    return $choice->value;}
             ])
            
            // Imbriquation formulaire voir instructor > create_question.html.twig
            ->add('proposal', CollectionType::class, [ // voir question.php @manyToMany > cascade -> important pour l'enregistrement en base de donnée
                'entry_type' => ProposalFormType::class,
                'label' =>true,
                'allow_add' => true,
                'allow_delete' => true,
             ])
            /* TODO Enlever is_mandatory */
             ->add('is_mandatory')//temporaire
            

           // Intégration d'une autre entité dans un form
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
