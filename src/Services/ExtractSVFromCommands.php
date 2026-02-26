<?php

namespace App\Services;

use App\Entity\CommandProducts;
use App\Entity\Product;
use App\Entity\ProductSV;
use App\Entity\PromoPackProduct;
use App\Entity\UserCommandPackPromo;
use App\Entity\UserCommands;
use App\Repository\ProductSVRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class ExtractSVFromCommands
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
     * Calcul des SV correspondants à chaque commande
     * @param UserCommands[] $userCommands
     * @return array
     */
    public function getSVFromCommands(array $userCommands): ?array
    {
        $svs = [];

        foreach ($userCommands as $command) {
            $svs[$command->getCode()] = $command->getTotalSVBinaire() ?? $this->sumSV($command->getProducts());
        }

        return $svs;
    }


    /**
     * Calcul des SV des achats personnels correspondants à chaque commande
     *
     * @param UserCommands[] $userCommands
     * @return array
     */
    public function getSVAchatPersonnel(array $userCommands): ?array
    {
        $svs = [];

        foreach ($userCommands as $command) {
            $svs[$command->getCode()] = $command->getTotalSVAP() ?? $this->sumSV($command->getProducts(), true);
        }

        return $svs;
    }

    /***
     * @param array $svCommands
     * @return int|float
     */
    public function sommeSVAchatPersonnel(array $svCommands)
    {
        $total = 0;

        foreach ($svCommands as $code => $sv) {
            $total += $sv;
        }

        return $total;
    }

    /**
     * @param UserCommandPackPromo[] $userCommandPacks
     * @return array
     */
    public function getSVAchatPack(array $userCommandPacks): ?array
    {
        $svs = [];

        foreach ($userCommandPacks as $command) {
            $svs[$command->getCode()] = $command->getQuantity() *
                $this->getSVOfProductConcerned($this->getProductConcernedInPack($command));
        }

        return $svs;
    }

    /**
     * @param UserCommandPackPromo $packPromo
     * @return PromoPackProduct[]|Collection
     */
    private function getProductConcernedInPack(UserCommandPackPromo $packPromo)
    {
        return $packPromo->getPack()->getProducts()->filter(function (PromoPackProduct $p) {
            return true === $p->getActive();
        });
    }

    /**
     * @param PromoPackProduct[]|Collection $products
     * @return float|int
     */
    private function getSVOfProductConcerned($products)
    {
        $total = 0;

        $products_sv = $this->svByProduct($products);

        if (!$products_sv || empty($products_sv)) {
            return $total;
        }

        return $this->sumSVpack($products, $products_sv);
    }

    /**
     * @param Collection $products
     * @return ProductSV[]|null
     */
    private function svByProduct(Collection $products)
    {
        /**
         * @var ProductSVRepository $repository
         */
        $repository = $this->manager->getRepository(ProductSV::class);

        /**
         * @var array<int> $product_ids
         */
        $product_ids = $this->getIdProductFromCollection($products);

        return  $repository->getSvOfProducts($product_ids);
    }

    /**
     * @param Collection $products
     * @return integer[]
     */
    private function getIdProductFromCollection(Collection $products)
    {
        if ($products->first() instanceof PromoPackProduct) {
            /**
             * @var integer[] $product_ids
             */
            $product_ids = $products->map(function (PromoPackProduct $p) {
                return $p->getProduct()->getId();
            });
        } else {
            /**
             * @var integer[] $product_ids
             */
            $product_ids = $products->map(function (CommandProducts $p) {
                return $p->getProduct()->getId();
            });
        }
        return $product_ids;
    }

    /**
     * @param CommandProducts[]|Collection $products
     * @param bool $achat_personal
     * @return float|int
     */
    private function sumSV($products, $achat_personal = false)
    {
        $total = 0;
        if ($achat_personal) {
            //dump($total);
            foreach ($products as $p) {
                $total += $p->getQuantity() * $p->getProduct()->getProductSVBPA();
            }
        } else {
            foreach ($products as $p) {
                $total += $p->getQuantity() * $p->getProduct()->getProductSV();
            }
        }

        return $total;
    }

    /**
     * @param Product $product
     * @param bool $achat_personal
     * @return float|int
     */
    public function processSVByType(Product $product, int $quantity, $achat_personal = false)
    {
        if ($achat_personal) {
            return $product->getProductSVBPA() * $quantity;
        } else {
            return $product->getProductSV() * $quantity;
        }
    }


    /**
     * @param PromoPackProduct[]|Collection $products
     * @param ProductSV[] $products_sv
     * @return float|int
     */
    private function sumSVpack($products, array $products_sv)
    {
        $total = 0;
        foreach ($products as $p) {
            foreach ($products_sv as $p_sv) {
                if ($p_sv->getProduct()->getId() === $p->getProduct()->getId()) {
                    $total += $p->getQuantityForSV() > 0 ?
                                    $p->getQuantityForSV() * $p_sv->getValue() :
                                    $p->getQuantity() * $p_sv->getValue() ;
                }
            }
        }
        return $total;
    }
}
