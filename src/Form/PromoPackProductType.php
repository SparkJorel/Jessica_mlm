<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\PromoPackProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoPackProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', NumberType::class, [
                'required' => true,
                'trim' => true,
                'html5' => true,
                'label' => 'Quantité',
            ])
            ->add('quantityForSV', NumberType::class, [
                'required' => true,
                'trim' => true,
                'html5' => true,
                'label' => 'Quantité à prendre en compte',
            ])
            ->add('active', CheckboxType::class, [
                'label' => false,
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'code',
                'placeholder' => '--Produits--',
                'required' => true,
                'label' => 'Product'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PromoPackProduct::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_promo_pack_pdct_form_token',
        ]);
    }
}
