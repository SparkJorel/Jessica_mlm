<?php

namespace App\Form\EventListener;

use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class AddServiceFieldSubscriber implements EventSubscriberInterface
{
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if ('service_prestation_edit' === $this->request
                                              ->getCurrentRequest()
                                              ->attributes->get('_route')) {
            $form->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'code',
                'label' => 'Service',
                'placeholder' => '--Service--'
            ])
            ;
        }
    }
}
