<?php

namespace App\Services\ModelHandlers;

use App\Form\ProductSVType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductSVHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(ProductSVType::class, $this->entity);
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
        // TODO: Implement save() method.
        if ($this->entity->isNew()) {
            return $this
                ->submit(
                    $request,
                    'product_sv_list',
                    'back/webcontroller/product_sv/new.html.twig',
                    'success',
                    'Product SV value created'
                );
        } else {
            return $this
                ->submit(
                    $request,
                    'product_sv_list',
                    'back/webcontroller/product_sv/new.html.twig',
                    'success',
                    'Product SV value updated'
                );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, '_jtwc_product_sv_token', 'jtwc_product_sv-delete')) {
            return $this->processRemovEntity('product_sv_list', 'info', 'Product SV value deleted');
        } else {
            return $this->redirectAfterSubmit('product_sv_list', 'danger', 'A problem occured when processing the request!!');
        }
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
        $product_svs = $this->getEntities();
        $product_svsView = $this
                            ->twig
                            ->render(
                                'back/webcontroller/product_sv/list.html.twig',
                                [
                                    'product_svs' => $product_svs
                                ]
                            );

        return new Response($product_svsView);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        // TODO: Implement show() method.
        return $this->getEntityView('back/webcontroller/product_sv/show.html.twig');
    }
}
