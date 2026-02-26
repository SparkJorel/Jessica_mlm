<?php

namespace App\Form\EventListener;

use App\Entity\UserCommands;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ClearCartListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit'
        ];
    }

    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $cart = $event->getData();

        if (!$cart instanceof UserCommands) {
            return ;
        }

        if (!$form->get('clear')->isClicked()) {
            return;
        }

        $cart->removeProducts();
    }
}
