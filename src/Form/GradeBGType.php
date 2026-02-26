<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\GradeBG;
use App\Entity\LevelBonusGenerationnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeBGType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lvl', EntityType::class, [
                'label' => 'Niveau',
                'class' => LevelBonusGenerationnel::class,
                'choice_label' => 'lvl',
                'placeholder' => '--choix du level--'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du level'
            ])
            ->add('value', NumberType::class, [
                'label' => 'Valeur du level',
            ])
            ->add('grade', EntityType::class, [
                'class' => Grade::class,
                'label' => 'Grade',
                'choice_label' => 'commercialName',
                'placeholder' => '--Grade--',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GradeBG::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_grade_bg_form_token',
            'validation_groups' => 'registration_grade_bg',
        ]);
    }
}
