<?php

namespace App\Form;

use App\Entity\Cycle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startedAt', DateTimeType::class, [
                'label' => 'Début',
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('endedAt', DateTimeType::class, [
                'label' => 'Fin',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('weekly', CheckboxType::class, [
                'label' => 'Cycle hebdomadaire ?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cycle::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_cycle_form_token',
            'validation_groups' => 'registration_cycle'
        ]);
    }
}
