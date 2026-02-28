<?php

namespace App\Controller\Back\WebController;

use App\Entity\IndirectBonusProduct;
use App\Services\ModelHandlers\IndirectBonusProductHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndirectBonusProductController
{
    /**
     * @var IndirectBonusProductHandler
     */
    private $indirectBonusProductHandler;

    public function __construct(IndirectBonusProductHandler $indirectBonusHandler)
    {
        $this->indirectBonusProductHandler = $indirectBonusHandler;
    }

    #[Route('/indirect-bonus-products', name: 'indirect_bonus_product_list', methods: ['GET'])]
    public function listIndirectBonusProduct()
    {
        $indirectBonusProduct = new IndirectBonusProduct();

        return $this->indirectBonusProductHandler->setEntity($indirectBonusProduct)->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-products/create', name: 'indirect_bonus_product_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $indirectBonusProduct = new IndirectBonusProduct();

        return  $this->indirectBonusProductHandler->setEntity($indirectBonusProduct)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-products/{id}/edit', name: 'indirect_bonus_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndirectBonusProduct $indirectBonusProduct)
    {
        return  $this->indirectBonusProductHandler->setEntity($indirectBonusProduct)->save($request);
    }

    #[Route('/indirect-bonus-product/{id}/show', name: 'indirect_bonus_product_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(IndirectBonusProduct $indirectBonusProduct)
    {
        return $this->indirectBonusProductHandler->setEntity($indirectBonusProduct)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-product/{id}/delete', name: 'indirect_bonus_product_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, IndirectBonusProduct $indirectBonusProduct)
    {
        return  $this
                    ->indirectBonusProductHandler
                    ->setEntity($indirectBonusProduct)
                    ->remove($request, $csrf);
    }
}
