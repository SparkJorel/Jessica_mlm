<?php

namespace App\Form\EventListener;

use App\Entity\User;
use App\Form\DataTransformer\SponsorAutocompleteTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddUplineFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if ($user instanceof User && (!$user || $user->isNew())) {
            $form
                ->add('upline', TextType::class, array(
                    'required' => false,
                    'label' => 'Upline',
                    'trim' => true,
                    'invalid_message' => 'The upline that you enter is not present.'
                ));
            $form->get('upline')
                ->getConfig()
                ->addModelTransformer(
                    new SponsorAutocompleteTransformer($this->manager)
                );
        }
    }
}
