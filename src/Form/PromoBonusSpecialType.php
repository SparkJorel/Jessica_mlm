<?php

namespace App\Form;

use App\Entity\BonusSpecial;
use App\Entity\Grade;
use App\Entity\PromoBonusSpecial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoBonusSpecialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startedAt', DateTimeType::class, [
                'label' => 'Début promotion',
            ])
            ->add('endedAt', DateTimeType::class, [
                'label' => 'Fin promotion'
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Activé cette promotion ?',
                'required' => false,
            ])
            ->add('underCondition', CheckboxType::class, [
                'label' => 'Offre soumise à condition ?',
                'required' => false,
            ])
            ->add('eligibleGrade', EntityType::class, [
                'class' => Grade::class,
                'label' => 'Grade'
            ])
            ->add('bonusSpecial', EntityType::class, [
                'class' => BonusSpecial::class,
                'label' => 'Grade'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PromoBonusSpecial::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_promo_bs_form_token',
            'validation_groups' => 'registration_promotion_bonus_special'
        ]);
    }
}
