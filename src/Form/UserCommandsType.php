<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\UserCommands;
use App\Form\DataTransformer\SponsorAutocompleteTransformer;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCommandsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $manager = $options['manager'];

        $builder
            ->add('user', TextType::class, [
                'label' => 'Client',
                'invalid_message' => 'le client que vous avez entré ne figure pas dans notre réseau',
                'required' => true,
                'trim' => true
            ]);
        $builder
                ->get('user')
                ->addModelTransformer(
                    new SponsorAutocompleteTransformer($manager)
                );
        $builder
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
            ->add('products', CollectionType::class, [
                'entry_type' => CommandProductsType::class,
                'block_name' => 'product_lists',
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
            'data_class' => UserCommands::class,
            'manager' => 'Doctrine\Persistence\ObjectManager',
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_user_cmds_form_token',
        ]);
    }
}
