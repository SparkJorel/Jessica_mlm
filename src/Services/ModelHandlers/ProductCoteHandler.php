<?php

namespace App\Services\ModelHandlers;

use App\Form\ProductCoteType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductCoteHandler extends ModelSingleEntityAbstract implements ModelInterface
{
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
                'product_cote_list',
                'back/webcontroller/product_cote/new.html.twig',
                'success',
                'La côte du produit a été créée'
            );
        } else {
            return $this->submit(
                $request,
                'product_cote_list',
                'back/webcontroller/product_cote/new.html.twig',
                'success',
                'La côte du produit a été modifiée'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_product_cote_token', 'jtwc_product_cote-delete')) {
            return $this->processRemovEntity('product_cote_list', 'info', 'Product Cote deactivated');
        } else {
            return $this->redirectAfterSubmit('product_cote_list', 'danger', 'A problem occured when processing the request!!');
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
        return new Response(
            $this->twig->render(
                'back/webcontroller/product_cote/show.html.twig',
                ['entity' => $this->entity]
            )
        );
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        $cotes = $this->getEntities();
        $coteView = $this
            ->twig
            ->render(
                'back/webcontroller/product_cote/list.html.twig',
                [
                    'cotes' => $cotes
                ]
            );

        return new Response($coteView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(ProductCoteType::class, $this->entity);
    }
}
