<?php

namespace App\Services\ModelHandlers;

use App\Form\IndirectBonusProductType;
use App\Entity\IndirectBonusProduct;
use App\Repository\IndirectBonusProductRepository;
use App\Services\ModelHandlers\ModelInterface;
use App\Services\ModelHandlers\ModelSingleEntityAbstract;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndirectBonusProductHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(IndirectBonusProductType::class, $this->entity);
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false)
    {
        if ($this->entity->isNew()) {
            return $this->submit(
                $request,
                'indirect_bonus_product_list',
                'back/webcontroller/indirect_bonus_product/new.html.twig',
                'success',
                'valeur créée'
            );
        } else {
            return $this->submit(
                $request,
                'indirect_bonus_product_list',
                'back/webcontroller/indirect_bonus_product/new.html.twig',
                'success',
                'valeur modifiée'
            );
        }
    }


    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_indirect_bonus_token', 'jtwc_indirect_bonus-delete')) {
            return $this->processRemovEntity('indirect_bonus_product_list', 'info', 'valeur supprimée');
        } else {
            return $this->redirectAfterSubmit('indirect_bonus_product_list', 'danger', 'A problem occured when processing the request!!');
        }
    }


    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        return $this
            ->getEntityView('back/webcontroller/indirect_bonus_product/show.html.twig');
    }


    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        // TODO: Implement list() method.
        $indirectBonusProducts = $this->getIndirectBonusProducts();

        $indirectBonusView
                        = $this
                            ->twig
                            ->render(
                                'back/webcontroller/indirect_bonus_product/list.html.twig',
                                [
                                    'indirectBonusProducts' => $indirectBonusProducts
                                ]
                            );

        return new Response($indirectBonusView);
    }


    protected function getIndirectBonusProducts(): ?array
    {
        /**
         * @var IndirectBonusProductRepository $repository
         */
        $repository = $this
                        ->manager
                        ->getRepository(get_class($this->entity));

        /** @var IndirectBonusProduct[]|null */
        $indirectBonusProducts = $repository->findAll();

        if (!$indirectBonusProducts) {
            return null;
        }

        $data = [];

        /**
         * @var IndirectBonusProduct $indirectBonusProduct
         */
        foreach ($indirectBonusProducts as $indirectBonusProduct) {
            $data[$indirectBonusProduct->getProduct()->getCode()][] = ['lvl' => $indirectBonusProduct->getLvl(), 'value' =>  $indirectBonusProduct->getValue(), 'id' =>  $indirectBonusProduct->getId()];
        }

        return $data;
    }
}
