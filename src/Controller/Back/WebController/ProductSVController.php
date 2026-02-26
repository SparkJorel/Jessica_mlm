<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductSV;
use App\Services\ModelHandlers\ProductSVHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/products-sv", name="product_sv_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->productSVHandler
                ->setEntity((new ProductSV()))
                ->list();
    }

    /**
     * @Route("/product-sv/{id}", name="product_sv_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param ProductSV $sv
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ProductSV $sv)
    {
        return $this->productSVHandler->setEntity($sv)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/products-sv/new", name="product_sv_new", methods={"GET","POST"})
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
                ->productSVHandler
                ->setEntity((new ProductSV()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/products-sv/{id}/edit", name="product_sv_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param ProductSV $sv
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, ProductSV $sv)
    {
        return
            $this
                ->productSVHandler
                ->setEntity($sv)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/products-sv/{id}/delete", name="product_sv_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param ProductSV $sv
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductSV $sv)
    {
        return  $this
                    ->productSVHandler
                    ->setEntity($sv)
                    ->remove($request, $csrf);
    }
}
