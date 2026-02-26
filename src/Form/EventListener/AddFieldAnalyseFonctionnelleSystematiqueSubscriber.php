<?php

namespace App\Form\EventListener;

use App\Entity\AnalyseFonctionnelleSystematique;
use App\Entity\PrestationService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldAnalyseFonctionnelleSystematiqueSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $entity = $event->getData();

        if ($entity instanceof PrestationService && null !== $entity->getService()->getId()) {
            $form
                ->add('analyseFonctionnelleSystematiques', EntityType::class, [
                'class' => AnalyseFonctionnelleSystematique::class,
                'label' => 'Analyse Fonctionnelle Systématique',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $repo) use ($entity) {
                    return $repo
                                ->createQueryBuilder('a')
                                ->addSelect('s')
                                ->innerJoin('a.services', 's')
                                ->where('s = :service')
                                ->setParameter('service', $entity->getService())
                        ;
                },
                'expanded' => true,
                'multiple' => true,
            ])
                ;
        }
    }
}
