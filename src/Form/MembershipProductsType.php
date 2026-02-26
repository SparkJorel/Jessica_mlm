<?php

namespace App\Form;

use App\Entity\MembershipProducts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('membershipProducts', CollectionType::class, [
                    'entry_type' => MembershipProductType::class,
                    'entry_options' => [
                        'label' => false
                    ],
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MembershipProducts::class
        ]);
    }
}
