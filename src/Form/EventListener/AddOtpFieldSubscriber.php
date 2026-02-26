<?php

namespace App\Form\EventListener;

use App\Entity\PurchaseSummary;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddOtpFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data instanceof PurchaseSummary) {
            return;
        }

        if ($data->getOperateur() === 2) {
            $form->add('otpCode', TextType::class, [
                'label' => 'Votre code OTP',
                'help' => 'Veuillez composer sur votre téléphone portable le code : <strong>#150*4*4#</strong> afin d\'obtenir votre code OTP'
            ]);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (array_key_exists('operateur', $data) &&
            array_key_exists('provider', $data) &&
            array_key_exists('montant', $data)) {
            $form->add('otpCode', TextType::class, [
                'label' => 'Votre code OTP',
                'help' => 'Veuillez composer sur votre téléphone portable le code : <strong>#150*4*4#</strong> afin d\'obtenir votre code OTP'
            ]);
        }
    }
}
