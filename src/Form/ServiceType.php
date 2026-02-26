<?php

namespace App\Form;

use App\Entity\AnalyseFonctionnelleSystematique;
use App\Entity\Service;
use App\Form\EventListener\AddCollectionPrestationServiceFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'Type de service',
                'required' => true,
                'trim' => true,
            ])
            ->add('code', TextType::class, [
                'label' => 'Code du service',
                'required' => true,
                'trim' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'trim' => true,
            ])
            ->add('analyseFonctionnelleSystematiques', EntityType::class, [
                'class' => AnalyseFonctionnelleSystematique::class,
                'label' => 'Analyse Fonctionnelle Systématique',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
            ])
        ;

        $builder->addEventSubscriber(new AddCollectionPrestationServiceFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_service_form_token',
            'validation_groups' => 'registration_service'
        ]);
    }
}
