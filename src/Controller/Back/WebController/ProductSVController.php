<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductSV;
use App\Services\ModelHandlers\ProductSVHandler;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ProductSVController
 * @package App\Controller\Back\WebController
 */
class ProductSVController
{
    /**
     * @var ProductSVHandler
     */
    private $productSVHandler;

    public function __construct(ProductSVHandler $productSVHandler)
    {
        $this->productSVHandler = $productSVHandler;
    }

    #[Route('/products-sv', name: 'product_sv_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->productSVHandler
                ->setEntity((new ProductSV()))
                ->list();
    }

    #[Route('/product-sv/{id}', name: 'product_sv_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ProductSV $sv)
    {
        return $this->productSVHandler->setEntity($sv)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products-sv/new', name: 'product_sv_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->productSVHandler
                ->setEntity((new ProductSV()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products-sv/{id}/edit', name: 'product_sv_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ProductSV $sv)
    {
        return
            $this
                ->productSVHandler
                ->setEntity($sv)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products-sv/{id}/delete', name: 'product_sv_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductSV $sv)
    {
        return  $this
                    ->productSVHandler
                    ->setEntity($sv)
                    ->remove($request, $csrf);
    }
}
