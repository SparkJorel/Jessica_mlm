<?php

namespace App\Form;

use App\Entity\Grade;
use App\Form\EventListener\AddGradeNewFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commercialName', TextType::class, [
                'label' => 'Grade'
            ])
            ->add('technicalName', TextType::class, [
                'label' => 'Nom technique'
            ])
            ->add('maintenance', NumberType::class, [
                'label' => 'Maintenance',
            ])
            ->add('lvl', NumberType::class, [
                'label' => 'Nombre de génération'
            ])
            ->add('sv', NumberType::class, [
                'label' => 'Volume SV',
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids',
                'required' => false,
                'help' => '1, 2, 3, 4,...'
            ])
            ->add('rewardable', CheckboxType::class, [
                'label' => 'Existe-t-il un bonus spécial associé à ce grade ?',
                'required' => false,
                'block_prefix' => 'grade_rewardable',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
        ;
        $builder->addEventSubscriber(new AddGradeNewFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_grade_form_token',
            'validation_groups' => 'registration_grade',
        ]);
    }
}
