<?php

namespace App\Services;

use App\Entity\CommandProducts;
use App\Entity\Cycle;
use App\Entity\ParameterConfig;
use App\Entity\ProductDistributorPrice;
use App\Entity\ProductSV;
use App\Repository\CommandProductsRepository;
use App\Repository\ParameterConfigRepository;
use App\Repository\ProductDistributorPriceRepository;
use App\Repository\ProductSVRepository;
use App\Repository\CycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MonthlyTurnOver
{
    use UtilitiesTrait;
    use ComputeBinaryTurnOverTrait;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var CloseCycle
     */
    private $closeCycle;

    public function __construct(
        CloseCycle $closeCycle,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $manager,
        Environment $twig
    )
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->closeCycle = $closeCycle;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @param Cycle|null $cycle
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function turnOver(Request $request, Cycle $cycle = null)
    {
        $turn_over = [];
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Cycle $cycle */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {

            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);

            $cycle = $repositoryCycle->getLastCycle();
        }

        /**
         * @var ParameterConfigRepository $repositoryCycle
         */
        $repositoryCycle = $this->manager->getRepository(ParameterConfig::class);

        $sv = $repositoryCycle->valueParameter('sv', $cycle);

        if ($cycle->getAutoSave()) {
            $turn_over['sponsorship'] = $this->getSumPriceMembershipSubscription($cycle);
            $turn_over['sponsorship_sv'] = $this->getSumSVMembershipSubscription($cycle);
        } else {
            $subscriptions = $this->getAllSubscriptionOfCycle($cycle);

            $turn_over['sponsorship'] = $this->getSponsorshipCostOfCycle($subscriptions);
            $turn_over['sponsorship_sv'] = $this->getSvGroupeNetwork($subscriptions);
        }


        $purchases = $this->getCyclePurchases($cycle);

        $turn_over['purchase'] = $this->getCostCyclePurchases($purchases);

        $turn_over['purchase_sv'] = $this->getTotalBinarySVPurchases($purchases);

        $turn_over['sv_binaire'] = $turn_over['purchase_sv'] + $turn_over['sponsorship_sv'];

        $turn_over['cout_sv_binaire'] = $turn_over['sv_binaire'] * $sv->getValue();

        $turn_over['turn_over'] = $turn_over['sponsorship'] + $turn_over['purchase'];

        $turn_over['recap'] = $this->getBonusDistributed($cycle);

        return $this
                    ->twig
                    ->render(
                        'back/webcontroller/turn_over/turn_over.html.twig',
                        [
                            'turn_over' => $turn_over,
                            'form' => $form->createView()
                        ]
                    );
    }

    protected function getBonusDistributed(Cycle $cycle)
    {
        $bonus = [];

        $report = $this->closeCycle->viewReport($cycle);

        $bonus['total'] = $report['total'];
        $bonus['passif'] = $report['passif'];
        $bonus['actif'] = $report['actif'];

        unset($report);

        return $bonus;
    }

    /**
     * @param CommandProducts[] $cyclePurchases
     * @return int|float
     */
    protected function getCostCyclePurchases(array $cyclePurchases)
    {
        $total = 0;

        if (empty($cyclePurchases)) {
            return $total;
        }

        foreach ($cyclePurchases as $purchase) {
            $total += !$purchase->getItemDistributorPrice() ? $purchase->getTotalDistributorPrice() : $purchase->getItemDistributorPrice();
        }

        return $total;
    }

    /**
     * @param Cycle $cycle
     * @return CommandProducts[]
     */
    protected function getCyclePurchases(Cycle $cycle)
    {
        /**
         * @var CommandProductsRepository $repository
         */
        $repository = $this->manager->getRepository(CommandProducts::class);

        return $repository->getAllProductsBoughtByCycle($cycle, true);
    }

    /**
     * @param int $product
     * @param Cycle $cycle
     * @return ProductDistributorPrice|null
     */
    protected function getDistributorPrice(int $product, Cycle $cycle)
    {
        /**
         * @var ProductDistributorPriceRepository $repository
         */
        $repository = $this->manager->getRepository(ProductDistributorPrice::class);

        return $repository->productCost($product, $cycle);
    }


    /**
     * @param array $product_ids
     * @param Cycle $cycle
     * @return array
     */
    protected function getDistributorPricesProducts(array $product_ids, Cycle $cycle): array
    {
        $pd_prices = [];

        foreach ($product_ids as $product_id) {
            $distributorPrice = $this->getDistributorPrice($product_id, $cycle);
            if ($distributorPrice) {
                $pd_prices[$distributorPrice->getProduct()->getId()] = $distributorPrice->getPrice();
            }
        }

        return $pd_prices;
    }

    /**
     * Cette méthode rétourne la valeur du binaire d'un produit à un cycle précis
     * @param int $product
     * @param Cycle $cycle
     * @return ProductSV|null
     */
    protected function getSVProduct(int $product, Cycle $cycle)
    {
        /**
         * @var ProductSVRepository $repository
         */
        $repository = $this->manager->getRepository(ProductSV::class);

        return $repository->productSV($product, $cycle);
    }

    /**
     * Cette méthode retourne la somme totale des SV correspondants
     * au volume total des achats d'un cycle.
     *
     * @param CommandProducts[] $cyclePurchases
     * @return int|float
     */
    protected function getTotalBinarySVPurchases(array $cyclePurchases)
    {
        $total = 0;

        if (empty($cyclePurchases)) {
            return $total;
        }

        foreach ($cyclePurchases as $purchase) {
            $total += !$purchase->getItemSVBinaire() ? $purchase->getTotalItemSVBinaire() : $purchase->getItemSVBinaire();
        }

        return $total;
    }
}
