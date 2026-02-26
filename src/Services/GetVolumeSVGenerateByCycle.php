<?php

namespace App\Services;

use App\Entity\CommandProducts;
use App\Entity\Cycle;
use App\Entity\MembershipSubscription;
use App\Entity\MembershipSV;
use App\Entity\ProductSV;
use App\Entity\PromoPackProduct;
use App\Entity\User;
use App\Entity\UserCommandPackPromo;
use App\Repository\CycleRepository;
use App\Repository\MembershipSubscriptionRepository;
use App\Repository\MembershipSVRepository;
use App\Repository\CommandProductsRepository;
use App\Repository\ProductSVRepository;
use App\Repository\UserCommandPackPromoRepository;
use App\Repository\PackPromoRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetVolumeSVGenerateByCycle
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
     * @param User $user
     * @param Cycle|null $cycle
     * @return float
     */
    public function getVolumeAchatSVByCycle(User $user, Cycle $cycle = null)
    {
        if (!$cycle) {
            /** @var CycleRepository $repository */
            $repository = $this->manager->getRepository(Cycle::class);
            $cycle = $repository->getLastCycle();
        }

        $packs = $this->getAchatPackByUserCycle($user, $cycle);
        $commandProducts = $this->getAchatByUserCycle($user, $cycle);
        $volumeSVAchatPackByCycle = $this->convertVolumeAchatPackInSV($packs);
        $volumeSVAchatproductByCycle = $this->convertVolumeAchatInSV($commandProducts);

        return $volumeSVAchatPackByCycle + $volumeSVAchatproductByCycle;
    }

    /**
     * @param User $user
     * @param Cycle|null $cycle
     * @return float|int
     */
    public function getVolumeSVSubscriptionByCycle(User $user, Cycle $cycle = null)
    {
        if (!$cycle) {
            /** @var CycleRepository $repository */
            $repository = $this->manager->getRepository(Cycle::class);

            $cycle = $repository->getLastCycle();
        }

        return $this->getSVSubscription($user, $cycle);
    }


    /** BONUS DE GROUPE APRES SUBSCRIPTION **/

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return int|float
     */
    private function getSVSubscription(User $user, Cycle $cycle)
    {
        /** @var MembershipSubscriptionRepository $repository */
        $repository = $this->manager
                        ->getRepository(MembershipSubscription::class);
        /**
         * @var MembershipSubscription[] $subscriptions
         */
        $subscriptions = $repository->getSubscription($user, $cycle);

        if (!$subscriptions || empty($subscriptions)) {
            return 0;
        } else {
            $total = 0;

            $mbship_id = $this->extractMembershipId($subscriptions);
            $membershipSV = $this->getSVMatchingMembershipSubscribed($mbship_id);
            $sv_groupe = $this->getSVGroupeByMembership($membershipSV);

            foreach ($subscriptions as $subscription) {
                $total += $subscription->isPaid() ? $sv_groupe[$subscription->getMembership()->getId()] : 0;
            }
            return $total;
        }
    }

    /**
     * @param array $membership
     * @return MembershipSV[]|null
     */
    private function getSVMatchingMembershipSubscribed(array $membership)
    {
        /** @var MembershipSVRepository $repository */
        $repository = $this->manager
                            ->getRepository(MembershipSV::class);

        return $repository->getSVMembership($membership);
    }

    /**
     * @param MembershipSV[] $svMembership
     * @return array
     */
    private function getSVGroupeByMembership($svMembership)
    {
        $sv_groupe = [];
        foreach ($svMembership as $sv_m) {
            $sv_groupe[$sv_m->getMembership()->getId()] = $sv_m->getSvGroupe();
        }

        return $sv_groupe;
    }

    /**
     * @param MembershipSubscription[] $memberships
     * @return array
     */
    private function extractMembershipId($memberships)
    {
        $memberships_id = [];
        foreach ($memberships as $mbership) {
            if (!in_array($mbership->getMembership()->getId(), $memberships_id)) {
                $memberships_id[] = $mbership->getMembership()->getId();
            }
        }
        return $memberships_id;
    }

    /** FIN BONUS GROUPE APRES SUBSCRIPTION **/

    /** ACHAT PACK ET COMMANDE PERSONNEL **/

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return CommandProducts[]|null
     */
    private function getAchatByUserCycle(User $user, Cycle $cycle): ?array
    {
        /** @var CommandProductsRepository $repository */
        $repository = $this->manager->getRepository(CommandProducts::class);

        return $repository->getVolumeAchatByUserByCycle($user, $cycle);
    }

    /**
     * @param User $user
     * @param Cycle|null $cycle
     * @return UserCommandPackPromo[]|null
     */
    private function getAchatPackByUserCycle(User $user, Cycle $cycle): ?array
    {
        /** @var UserCommandPackPromoRepository $repository */
        $repository = $this->manager->getRepository(UserCommandPackPromo::class);
        return $repository->getAchatPackPromoByUserByCycle($cycle, $user);
    }

    /**
     * @param CommandProducts[] $commandProducts
     * @return float
     */
    private function convertVolumeAchatInSV($commandProducts): float
    {
        if (empty($commandProducts)) {
            return 0;
        } else {
            $total_sv = 0;
            $products_id = $this->extractProductId($commandProducts);
            $products_sv = $this->getSVByProduct($products_id);

            if (!$products_sv) {
                return 0;
            }

            foreach ($commandProducts as $product) {
                $total_sv += $product->getQuantity() * $products_sv[$product->getProduct()->getId()];
            }
            return $total_sv;
        }
    }

    /**
     * @param UserCommandPackPromo[] $packs
     * @return float
     */
    private function convertVolumeAchatPackInSV($packs): float
    {
        $packs_id = [];

        if (empty($packs)) {
            return 0;
        } else {
            $total_sv = 0;
            foreach ($packs as $pack) {
                if (!in_array($pack->getPack()->getId(), $packs_id)) {
                    $packs_id[] = $pack->getPack()->getId();
                }
            }

            $products = $this->getProductConcernedByPromo($packs_id);
            $products_id = $this->extractProductIdInPack($products);
            $products_sv = $this->getSVByProduct($products_id);

            if (!$products_sv) {
                return 0;
            }

            foreach ($packs as $pack) {
                $sv_pack = $this
                                ->computeSVByPack(
                                    $products[$pack->getPack()->getId()],
                                    $products_sv
                                );
                $total_sv += $sv_pack * $pack->getQuantity();
            }

            return $total_sv;
        }
    }

    /**
     * @param array $productPacks
     * @param array $product_sv
     * @return float|int
     */
    private function computeSVByPack(array $productPacks, array $product_sv)
    {
        $somme = 0;
        foreach ($productPacks as $product) {
            $somme += $product['quantity'] * $product_sv[$product['product']];
        }
        return $somme;
    }

    /**
     * @param array $products
     * @return array|null
     */
    private function getSVByProduct(array $products)
    {
        $product_sv = [];

        /** @var ProductSVRepository $repository */
        $repository = $this->manager->getRepository(ProductSV::class);
        /**
         * @var ProductSV[]|null $products_sv
         */
        $products_sv =  $repository->getSvOfProducts($products);

        if (!$products_sv || empty($products_sv)) {
            return null;
        }

        foreach ($products_sv as $p_sv) {
            $product_sv[$p_sv->getProduct()->getId()] = $p_sv->getValue();
        }

        return $product_sv;
    }

    /**
     * cette méthode retourne la liste des produits dont les sv doivent être comptabilisés dans les
     * bonus de groupe et achat personnel
     *
     * @param array $packs
     * @return array
     */
    private function getProductConcernedByPromo(array  $packs): array
    {
        /** @var PromoPackProductRepository $repository */
        $repository = $this->manager->getRepository(PromoPackProduct::class);

        $products = [];
        /**
         * @var PromoPackProduct[] $promoProducts
         */
        $promoProducts = $repository->getProductsConcernedBySV($packs);

        if (!empty($promoProducts)) {
            foreach ($promoProducts as $product) {
                if ($product->getActive()) {
                    $products[$product->getPromo()->getId()][] = [
                        'product' => $product->getProduct()->getId(),
                        'quantity' => $product->getQuantityForSV() === 0 ?
                                             $product->getQuantity() : $product->getQuantityForSV()
                    ];
                }
            }
        }
        return $products;
    }

    private function extractProductIdInPack(array $products)
    {
        $products_id = [];
        foreach ($products as $pack_id => $product) {
            foreach ($product as $p) {
                if (!in_array($p['product'], $products_id)) {
                    $products_id[] = $p['product'];
                } else {
                    continue;
                }
            }
        }

        return $products_id;
    }

    /**
     * @param CommandProducts[] $products
     * @return array
     */
    private function extractProductId($products)
    {
        $products_id = [];
        foreach ($products as $product) {
            if (!in_array($product->getProduct()->getId(), $products_id)) {
                $products_id[] = $product->getProduct()->getId();
            }
        }
        return $products_id;
    }
}
