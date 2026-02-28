<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductClientPrice;
use App\Services\ModelHandlers\ProductClientPriceHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductClientPriceController
{
    /**
     * @var ProductClientPriceHandler
     */
    private $cpHandler;

    public function __construct(ProductClientPriceHandler $cpHandler)
    {
        $this->cpHandler = $cpHandler;
    }

    #[Route('/client-prices', name: 'product_cp_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->cpHandler
                ->setEntity((new ProductClientPrice()))
                ->list();
    }

    #[Route('/client-prices/{id}', name: 'product_cp_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProductClientPrice $price)
    {
        return $this->cpHandler->setEntity($price)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/client-prices/new', name: 'product_cp_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->cpHandler
                ->setEntity((new ProductClientPrice()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/client-prices/{id}/edit', name: 'product_cp_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ProductClientPrice $price)
    {
        return
            $this
                ->cpHandler
                ->setEntity($price)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/client-prices/{id}/delete', name: 'product_cp_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductClientPrice $price)
    {
        return  $this
                    ->cpHandler
                    ->setEntity($price)
                    ->remove($request, $csrf);
    }
}
