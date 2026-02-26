<?php

namespace App\Form;

use App\Entity\PackPromo;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackPromoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code Promo',
                'required' => true,
                'trim' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de la Promo',
                'required' => true,
                'trim' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'trim' => true,
            ])
            ->add('startedAt', DateTimeType::class, [
                'date_label' => 'Date de début',
                'html5' => true,
                'required' => true,
                'model_timezone' => 'Africa/Douala'
            ])
            ->add('endedAt', DateTimeType::class, [
                'date_label' => 'Date de fin',
                'html5' => true,
                'required' => true,
                'model_timezone' => 'Africa/Douala'
            ])
            ->add('products_list', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'code',
                'query_builder' => function (ProductRepository $repository) {
                    return $repository
                        ->createQueryBuilder('p')
                        ->where('p.status = :status')
                        ->setParameter('status', true)
                        ->orderBy('p.code', 'ASC')
                        ;
                },
                'mapped' => false,
                'required' => true,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('packProducts', CollectionType::class, [
                'entry_type' => PromoPackProductType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PackPromo::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_user_pack_promo_form_token',
        ]);
    }
}
