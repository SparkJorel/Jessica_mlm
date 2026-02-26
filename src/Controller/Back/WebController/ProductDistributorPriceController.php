<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductDistributorPrice;
use App\Services\ModelHandlers\ProductDistributorPriceHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/distributor-prices", name="product_dp_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity((new ProductDistributorPrice()))
                ->list();
    }



    /**
     * @Route("/distributor-prices/{id}", name="product_dp_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param ProductDistributorPrice $price
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ProductDistributorPrice $price)
    {
        return $this->distributorPriceHandler->setEntity($price)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/distributor-prices/new", name="product_dp_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity((new ProductDistributorPrice()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/distributor-prices/{id}/edit", name="product_dp_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param ProductDistributorPrice $price
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, ProductDistributorPrice $price)
    {
        return
            $this
                ->distributorPriceHandler
                ->setEntity($price)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/distributor-prices/{id}/delete", name="product_dp_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param ProductDistributorPrice $price
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductDistributorPrice $price)
    {
        return  $this
                    ->distributorPriceHandler
                    ->setEntity($price)
                    ->remove($request, $csrf);
    }
}
