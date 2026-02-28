<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductDistributorPrice;
use App\Services\ModelHandlers\ProductDistributorPriceHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductDistributorPriceController
{
    /**
     * @var ProductDistributorPriceHandler
     */
    private $distributorPriceHandler;

    public function __construct(ProductDistributorPriceHandler $distributorPriceHandler)
    {
        $this->distributorPriceHandler = $distributorPriceHandler;
    }

    #[Route('/distributor-prices', name: 'product_dp_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity((new ProductDistributorPrice()))
                ->list();
    }



    #[Route('/distributor-prices/{id}', name: 'product_dp_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProductDistributorPrice $price)
    {
        return $this->distributorPriceHandler->setEntity($price)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/distributor-prices/new', name: 'product_dp_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity((new ProductDistributorPrice()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/distributor-prices/{id}/edit', name: 'product_dp_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ProductDistributorPrice $price)
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity($price)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/distributor-prices/{id}/delete', name: 'product_dp_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductDistributorPrice $price)
    {
        return  $this
                    ->distributorPriceHandler
                    ->setEntity($price)
                    ->remove($request, $csrf);
    }
}
