<?php

namespace App\Services;

use App\AbstractModel\EntityInterface;
use App\Entity\UserCommandPackPromo;
use App\Repository\UserCommandPackPromoRepository;
use App\Repository\UserCommandsRepository;
use Doctrine\ORM\EntityManagerInterface;

class GenerateCode
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param EntityInterface $entity
     * @return bool|string
     */
    public function generateCode(EntityInterface $entity)
    {
        $code = '';
        $total = $this->getCount($entity);

        if (!$total) {
            return false;
        }

        if ('App\Entity\UserCommands' === get_class($entity)) {
            $code = (int)$total > 9 ? 'CMD'.$total : 'CMD0'.$total ;
        }

        return $code;
    }

    /**
     * @param EntityInterface $entity
     * @return bool|string
     */
    public function generateCodeCommandPack(EntityInterface $entity)
    {
        $code = '';
        $total = $this->getCountCommandPack($entity);

        if (!is_int($total)) {
            return false;
        }

        if ($entity instanceof UserCommandPackPromo) {
            $code = $total >= 9 ? $entity->getPack()->getCode().''.($total + 1) :
                                               $entity->getPack()->getCode().'0' . ($total + 1) ;
        }

        return $code;
    }

    /**
     * @param EntityInterface $entity
     * @return int|null
     */
    private function getCount(EntityInterface $entity)
    {
        $repository = $this->manager->getRepository(get_class($entity));
        if (!$repository instanceof  UserCommandsRepository) {
            return false;
        }

        $auto_increment = $repository->getNextAutoIncrementValue();
	  
	  	if (is_array($auto_increment)) {
            return $auto_increment['auto_increment'];
        } else {
            return null;
        }
    }

    /**
     * @param EntityInterface $entity
     * @return bool|int|null
     */
    private function getCountCommandPack(EntityInterface $entity)
    {
        $repository = $this->manager->getRepository(get_class($entity));
        if (!$repository instanceof UserCommandPackPromoRepository) {
            return false;
        }

        if (!$entity instanceof  UserCommandPackPromo) {
            return false;
        }

        return  $repository->getCount($entity->getPack()->getCode());
    }
}
