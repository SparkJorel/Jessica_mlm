<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductSV;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class, [
                'label' => 'Value',
                'required' => true,
                'trim' => true,
            ])
            ->add('valueBPA', NumberType::class, [
                'label' => 'SV BAP',
                'required' => true,
                'trim' => true,
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'required' => true,
                'choice_label' => 'name',
                'trim' => true,
                'placeholder' => '--make a choice--',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductSV::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_product_sv_form_token',
        ]);
    }
}
