<?php

namespace App\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Form\BonusGenerationnelLevelType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddGradeNewFieldSubscriber implements EventSubscriberInterface
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
        $grade = $event->getData();
        $form = $event->getForm();

        if (!$grade || null === $grade->getId()) {
            $form
                ->add('bgs', CollectionType::class, [
                    'entry_type' => BonusGenerationnelLevelType::class,
                    'entry_options' => [
                        'attr' => ['class' => 'bg-box'],
                        'label' => false,
                    ],
                    'label' => false,
                    'mapped' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]);
        }
    }
}
