<?php

namespace App\Form\EventListener;

use App\Entity\Membership;
use App\Repository\MembershipRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AddFieldMembershipSubscriber implements EventSubscriberInterface
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
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    protected function addMembershipForm(FormInterface $form, $member_id = null)
    {
        $form->add('membership', EntityType::class, array(
            'class' => Membership::class,
            'placeholder' => '--Membership--',
            'label' => 'Membership ',
            'query_builder' => function (MembershipRepository $repository) use ($member_id) {
                if (!$member_id) {
                    return null;
                }
                return $repository->getMembershipForUpgrade($member_id);
            },
            'choice_label' => 'name',
            'choice_value' => 'id'
        ));
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $member_id = $data->getMember() !== null ? $data->getMember()->getId() : null;
        $this->addMembershipForm($form, $member_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $member = array_key_exists('member', $data) ? $data['member'] : null;
        $this->addMembershipForm($form, $member);
    }
}
