<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductClientPrice;
use App\Services\ModelHandlers\ProductClientPriceHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/client-prices", name="product_cp_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->cpHandler
                ->setEntity((new ProductClientPrice()))
                ->list();
    }

    /**
     * @Route("/client-prices/{id}", name="product_cp_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param ProductClientPrice $price
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ProductClientPrice $price)
    {
        return $this->cpHandler->setEntity($price)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/client-prices/new", name="product_cp_new", methods={"GET","POST"})
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
                ->cpHandler
                ->setEntity((new ProductClientPrice()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/client-prices/{id}/edit", name="product_cp_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param ProductClientPrice $price
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, ProductClientPrice $price)
    {
        return
            $this
                ->cpHandler
                ->setEntity($price)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/client-prices/{id}/delete", name="product_cp_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param ProductClientPrice $price
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductClientPrice $price)
    {
        return  $this
                    ->cpHandler
                    ->setEntity($price)
                    ->remove($request, $csrf);
    }
}
