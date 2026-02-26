<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code produit',
                'required' => true,
                'trim' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom produit',
                'required' => true,
                'trim' => true,
            ])
            ->add('productSV', NumberType::class, [
                'label' => 'SV produit',
                'required' => true,
                'trim' => true,
            ])
            ->add('productSVBPA', NumberType::class, [
                'label' => 'SV BAP',
                'required' => true,
                'trim' => true,
            ])
            ->add('distributorPrice', NumberType::class, [
                'label' => 'Prix distributeur',
                'required' => true,
                'trim' => true,
            ])
            ->add('clientPrice', NumberType::class, [
                'label' => 'Prix client',
                'required' => true,
                'trim' => true,
            ])
            ->add('productCote', NumberType::class, [
                'label' => 'Côte produit',
                'required' => true,
                'trim' => true,
            ])
            ->add('description', CKEditorType::class, array(
                'label' => 'Description',
                'required' => false,
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ))
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image produit',
                'required' => false,

                'constraints' => [
                    new File([
                        'maxSize' => '6020k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_product_form_token',
            'validation_groups' => 'registration_product'
        ]);
    }
}
