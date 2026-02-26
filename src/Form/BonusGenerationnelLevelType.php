<?php

namespace App\Form;

use App\Entity\LevelBonusGenerationnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BonusGenerationnelLevelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level', EntityType::class, [
                'label' => 'Nom',
                'class' => LevelBonusGenerationnel::class,
                'choice_label' => 'lvl',
                'required' => true,
                'trim' => true,
                'help' => '(exemple: 1)',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'trim' => true,
                'help' => '(exemple: gene1)',
            ])
            ->add('value', NumberType::class, [
                'label' => 'Valeur',
                'required' => true,
                'trim' => true,
                'help' => '(exemple: 0.1)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_bgl_form_token',
        ]);
    }
}
