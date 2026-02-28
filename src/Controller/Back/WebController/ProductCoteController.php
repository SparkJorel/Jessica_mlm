<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductCote;
use App\Services\ModelHandlers\ProductCoteHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductCoteController
{
    /**
     * @var ProductCoteHandler
     */
    private $productCoteHandler;

    public function __construct(ProductCoteHandler $productCoteHandler)
    {
        $this->productCoteHandler = $productCoteHandler;
    }

    #[Route('/product-cotes', name: 'product_cote_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->productCoteHandler
                ->setEntity((new ProductCote()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/product-cotes/new', name: 'product_cote_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->productCoteHandler
                ->setEntity((new ProductCote()))
                ->save($request)
            ;
    }

    #[Route('/product-cotes/{id}', name: 'product_cote_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProductCote $productCote)
    {
        return $this->productCoteHandler->setEntity($productCote)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/product-cotes/{id}/edit', name: 'product_cote_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ProductCote $productCote)
    {
        return
            $this
                ->productCoteHandler
                ->setEntity($productCote)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/product-cotes/{id}/delete', name: 'product_cote_delete', methods: ['POST', 'GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductCote $productCote)
    {
        return  $this
            ->productCoteHandler
            ->setEntity($productCote)
            ->remove($request, $csrf);
    }
}
