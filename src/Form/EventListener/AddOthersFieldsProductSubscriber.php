<?php

namespace App\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddOthersFieldsProductSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $product = $event->getData();
        $form = $event->getForm();

        if (!$product || null === $product->getId()) {
            $form
                ->add('cote', NumberType::class, [
                    'label' => 'Côte',
                    'mapped' => false,
                    'required' => true,
                    'trim' => true,
                ])
                ->add('distributorPrice', NumberType::class, [
                    'label' => 'Prix distributeur',
                    'mapped' => false,
                    'required' => true,
                    'trim' => true,
                ])
                ->add('clientPrice', NumberType::class, [
                    'label' => 'Prix client',
                    'mapped' => false,
                    'required' => true,
                    'trim' => true,
                ])
                ->add('svbb', NumberType::class, [
                    'label' => 'SV Bonus binaire',
                    'mapped' => false,
                    'required' => true,
                    'trim' => true,
                ])
                ->add('svbap', NumberType::class, [
                    'label' => 'SV BAP',
                    'mapped' => false,
                    'required' => true,
                    'trim' => true,
                ])
            ;
        }
    }
}
