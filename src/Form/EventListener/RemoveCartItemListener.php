<?php

namespace App\Form\EventListener;

use App\Entity\UserCommands;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RemoveCartItemListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit'
        ];
    }

    public function postSubmit(FormEvent $event)
    {
        $cart = $event->getData();
        $form = $event->getForm();

        if (!$cart instanceof UserCommands) {
            return;
        }

        /** @var FormInterface $child */
        foreach ($form->get('products')->all() as $child) {
            if ($child->get('remove')->isClicked()) {
                $cart->removeProduct($child->getData());
                break;
            }
            if ($child->get('update')->isClicked()) {
                $cart->updateProductQuantity($child->getData());
                break;
            }
        }
    }
}
