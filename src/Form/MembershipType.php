<?php

namespace App\Form;

use App\Entity\Membership;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
                'required' => true,
                'trim' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'trim' => true,
            ])
            ->add('coefficent', ChoiceType::class, [
                'label' => 'Coefficient',
                'required' => true,
                'trim' => true,
                'choices' => [
				  	'0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
				  	'4' => 4,
                ],
                'placeholder' => '--make a choice--'
            ])
            ->add('membershipCost', NumberType::class, [
                'label' => 'Coût du pack',
                'required' => true,
                'trim' => true,
            ])
            ->add('membershipGroupeSV', NumberType::class, [
                'label' => 'Volume SV du binaire',
                'required' => true,
                'trim' => true,
            ])
            ->add('membershipProductCote', NumberType::class, [
                'label' => 'Quote du pack',
                'required' => true,
                'trim' => true,
            ])
            ->add('membershipBonusBinairePourcent', NumberType::class, [
                'label' => 'Pourcentage binaire',
                'required' => true,
                'trim' => true,
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'trim' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Membership::class,
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_jtwc_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id' => 'jtwc_membership_form_token',
            'validation_groups' => 'registration_membership',
        ]);
    }
}
