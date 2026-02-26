<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductCote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => '--Choisir un produit--',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'trim' => true,
            ])
            ->add('value', NumberType::class, [
                'label' => 'Côte',
                'required' => true,
                'trim' => true,
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCote::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_product_cote_form_token',
            'validation_groups' => ['registration_product_cote']
        ]);
    }
}
