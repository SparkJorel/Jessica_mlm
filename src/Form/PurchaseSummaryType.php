<?php

namespace App\Form;

use App\Entity\PurchaseSummary;
use App\Form\EventListener\AddOtpFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseSummaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('telephone', IntegerType::class, [
            'label' => 'Numéro téléphone',
            'mapped' => false,
            'required' => true,
            'help' => '6XXXXXXXX (pas d\'espace dans votre numéro merci)'
        ]);

        $builder->addEventSubscriber(new AddOtpFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PurchaseSummary::class
        ]);
    }
}
