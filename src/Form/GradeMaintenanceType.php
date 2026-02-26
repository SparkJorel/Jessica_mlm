<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\GradeMaintenance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeMaintenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maintenance', TextType::class, [
                'label' => 'Maintenance'
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
            'data_class' => GradeMaintenance::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_grade_maintenance_form_token',
            'validation_groups' => 'registration_grade_maintenance',
        ]);
    }
}
