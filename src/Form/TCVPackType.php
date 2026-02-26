<?php

namespace App\Form;

use App\Entity\TVCPack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TCVPackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('membership')
            ->add('value')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TVCPack::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_tvc_pack_form_token',
            'validation_groups' => ['registration'],
        ]);
    }
}
