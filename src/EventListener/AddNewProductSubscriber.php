<?php

namespace App\EventListener;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Events;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AddNewProductSubscriber
{

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $manager = $args->getObjectManager();

        if (!$entity instanceof Product) {
            return;
        }

        $entity->setStatus(true);


        /**
         * @var ProductRepository $repository
         */
        $repository = $manager->getRepository(Product::class);

        /**
         * @var Product|null $product
         */
        $product = $repository->getLastProduct($entity);

        if (!$product) {
            return ;
        }

        $endedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));

        $product->setStatus(false)->setUpdatedAt($endedAt);
    }
}
