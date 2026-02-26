<?php

namespace App\Form;

use App\Entity\FiltreProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('product', TextType::class, [
            'label' => 'Produit',
            'required' => true,
            'trim' => true,
        ])
        ->add('quantity', IntegerType::class, [
            'label' => 'Quantité',
            'required' => true,
            'trim' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FiltreProduct::class,
            'csrf_protection' => false,
            'method' => 'get',
        ]);
    }
}
