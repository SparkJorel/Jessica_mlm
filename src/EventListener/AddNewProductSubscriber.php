<?php

namespace App\EventListener;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Events;
use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use App\Repository\ProductRepository;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class AddNewProductSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

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
