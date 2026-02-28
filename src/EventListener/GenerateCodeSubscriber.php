<?php

namespace App\EventListener;

use App\Entity\UserCommandPackPromo;
use App\Entity\UserCommands;
use App\Exception\GenerateCodeException;
use App\Services\GenerateCode;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class GenerateCodeSubscriber implements EventSubscriber
{
    /**
     * @var GenerateCode
     */
    private $generateCode;

    public function __construct(GenerateCode $generateCode)
    {
        $this->generateCode = $generateCode;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws GenerateCodeException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserCommands) {
            return ;
        }

        $code = $this->generateCode->generateCode($entity);

        if (!is_string($code) || empty($code)) {
            throw new GenerateCodeException("The system was not able to generate the code. Please submit the form again.");
        }
	  
	  	$code = $entity->getUser()->getCodeDistributor().'-'.$code;

        $entity->setCode($code);
    }
}
