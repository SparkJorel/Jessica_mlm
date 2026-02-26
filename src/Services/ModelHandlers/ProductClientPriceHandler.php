<?php

namespace App\Services\ModelHandlers;

use App\Form\ProductClientPriceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductClientPriceHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this
                    ->formFactory
                    ->create(
                        ProductClientPriceType::class,
                        $this->entity
                    );
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
            return $this->submit(
                $request,
                'product_cp_list',
                'back/webcontroller/product_client_price/new.html.twig',
                'success',
                'New Product client Price created'
            );
        } else {
            return $this
                ->submit(
                    $request,
                    'product_cp_list',
                    'back/webcontroller/product_client_price/new.html.twig',
                    'success',
                    'Product client price updated'
                );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, '_jtwc_product_client_price_token', 'jtwc_product_client_price-delete')) {
            return $this
                ->processRemovEntity(
                    'product_cp_list',
                    'info',
                    'Product client price deactivated'
                );
        } else {
            return $this
                ->redirectAfterSubmit(
                    'product_cp_list',
                    'danger',
                    'A problem occured when processing the request!!'
                );
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
        $product_cps = $this->getEntities();
        $product_cpsView = $this
            ->twig
            ->render(
                'back/webcontroller/product_client_price/list.html.twig',
                [
                    'product_cps' => $product_cps
                ]
            );

        return new Response($product_cpsView);
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
        return $this->getEntityView('back/webcontroller/product_client_price/show.html.twig');
    }
}
