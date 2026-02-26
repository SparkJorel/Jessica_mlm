<?php

namespace App\Form\EventListener;

use App\Form\PrestationServiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddCollectionPrestationServiceFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $service = $event->getData();
        $form = $event->getForm();

        if (!$service || null === $service->getId()) {
            $form
                ->add('prestationServices', CollectionType::class, [
                    'entry_type' => PrestationServiceType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]);
        }
    }
}
