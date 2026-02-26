<?php

namespace App\Form;

use App\Entity\Membership;
use App\Entity\MembershipSV;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('svProduct', NumberType::class, [
                'label' => 'Nombre des SV des Achats',
                'required' => true,
                'trim' => true,
            ])
            ->add('svGroupe', NumberType::class, [
                'label' => 'Nombre des SV du bonus de groupe',
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
            'data_class' => MembershipSV::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_mbship_sv_form_token',
            'validation_groups' => ['registration'],
        ]);
    }
}
