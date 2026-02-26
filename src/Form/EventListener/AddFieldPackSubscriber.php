<?php

namespace App\Form\EventListener;

use App\Entity\CompositionMembershipProductName;
use App\Entity\Membership;
use App\Entity\User;
use App\Repository\CompositionMembershipProductNameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\HttpFoundation\RequestStack;

class AddFieldPackSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        /** @var User $data */
        $data = $event->getData();
        $form = $event->getForm();

        if ('new_user_update' === $this->requestStack->getCurrentRequest()->attributes->get('_route')) {

            /** @var Membership $membership */
            $membership = $data->getMembership();

            $form
                ->add('pack', EntityType::class, [
                    'class' => CompositionMembershipProductName::class,
                    'query_builder' => function (CompositionMembershipProductNameRepository $repository) use ($membership) {
                        return $repository->getAvailablePackName($membership);
                    },
                    'label' => 'Composition de vos produits',
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => '--Choix d\'une composition--',
                ]);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ('new_user_update' === $this->requestStack->getCurrentRequest()->attributes->get('_name')) {

            /** @var Membership $membership */
            $membership = array_key_exists('membership', $data) ? $data['membership'] : null;

            $form
                ->add('pack', EntityType::class, [
                    'class' => CompositionMembershipProductName::class,
                    'queryBuilder' => function (CompositionMembershipProductNameRepository $repository) use ($membership) {
                        return $repository->getAvailablePackName($membership);
                    },
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => '--Choix d\'une composition--',
                ]);
        }
    }
}
