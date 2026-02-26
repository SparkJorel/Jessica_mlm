<?php

namespace App\Form;

use App\Entity\CompositionMembershipProductName;
use App\Entity\MembershipProduct;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
                'label' => 'Nom',
                'class' => CompositionMembershipProductName::class,
                'placeholder' => '--Choix du nom--',
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité'
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Produit',
                'placeholder' => '--Choix du produit--',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MembershipProduct::class,
        ]);
    }
}
