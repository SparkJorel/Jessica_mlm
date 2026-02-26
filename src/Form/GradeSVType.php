<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\GradeSV;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sv', TextType::class, [
                'label' => 'Volume de SV'
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
            'data_class' => GradeSV::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_grade_sv_form_token',
            'validation_groups' => 'registration_grade_sv',
        ]);
    }
}
