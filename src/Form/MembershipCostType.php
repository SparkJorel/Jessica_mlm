<?php

namespace App\Form;

use App\Entity\Membership;
use App\Entity\MembershipCost;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipCostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class, [
                'label' => 'Value',
                'required' => true,
                'trim' => true,
            ])
            ->add('membership', EntityType::class, [
                'class' => Membership::class,
                'choice_label' => 'name',
                'placeholder' => '--choisir un membership--',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MembershipCost::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_mbship_cost_form_token',
            'validation_groups' => ['registration']
        ]);
    }
}
