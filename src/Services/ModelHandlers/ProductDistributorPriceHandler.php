<?php

namespace App\Services\ModelHandlers;

use App\Form\ProductDistributorPriceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductDistributorPriceHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this
                    ->formFactory
                    ->create(
                        ProductDistributorPriceType::class,
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
                'product_dp_list',
                'back/webcontroller/product_distributor_price/new.html.twig',
                'success',
                'New Product Distributor Price created'
            );
        } else {
            return $this
                ->submit(
                    $request,
                    'product_dp_list',
                    'back/webcontroller/product_distributor_price/new.html.twig',
                    'success',
                    'Product Distributor Price updated'
                );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, 'jtwc_product_distributor_price_token', 'jtwc_product_distributor_price-delete')) {
            return $this
                    ->processRemovEntity(
                        'product_dp_list',
                        'info',
                        'Product distributor price deactivated'
                    );
        } else {
            return $this
                    ->redirectAfterSubmit(
                        'product_dp_list',
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
        $product_dps = $this->getEntities();
        $product_dpsView = $this
                                ->twig
                                ->render(
                                    'back/webcontroller/product_distributor_price/list.html.twig',
                                    [
                                        'product_dps' => $product_dps
                                    ]
                                );

        return new Response($product_dpsView);
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
        return $this
                ->getEntityView('back/webcontroller/product_distributor_price/show.html.twig');
    }
}
