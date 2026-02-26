<?php

namespace App\Form;

use App\Entity\LevelBonusGenerationnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LevelBonusGenerationnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lvl', TextType::class, [
                'label' => 'Level'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LevelBonusGenerationnel::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_level_bonus_generationnel_form_token',
            'validation_groups' => 'registration_level_bonus_generationnel',
        ]);
    }
}
