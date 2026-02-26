<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('oldPassword', PasswordType::class, [
                    'label' => 'Ancien mot de passe',
                    'mapped' => false,
                ])
                ->add('password', RepeatedType::class, array(
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les champs mot de passe doivent être identiques.',
                    'options'         => array('attr' => array('class' => 'password-field')),
                    'required'        => true,
                    'first_options'   => array('label' => 'Nouveau mot de passe'),
                    'second_options'  => array('label' => 'Confirmer'),
                ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_user_form_token',
        ]);
    }
}
