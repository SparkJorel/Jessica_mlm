<?php

namespace App\Controller\Back\WebController;

use App\Entity\ProductCote;
use App\Services\ModelHandlers\ProductCoteHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductCoteController
{
    /**
     * @var ProductCoteHandler
     */
    private $productCoteHandler;

    public function __construct(ProductCoteHandler $productCoteHandler)
    {
        $this->productCoteHandler = $productCoteHandler;
    }

    /**
     * @Route("/product-cotes", name="product_cote_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->productCoteHandler
                ->setEntity((new ProductCote()))
                ->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/product-cotes/new", name="product_cote_new", methods={"GET","POST"})
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
                ->productCoteHandler
                ->setEntity((new ProductCote()))
                ->save($request)
            ;
    }

    /**
     * @Route("/product-cotes/{id}", name="product_cote_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param ProductCote $productCote
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(ProductCote $productCote)
    {
        return $this->productCoteHandler->setEntity($productCote)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/product-cotes/{id}/edit", name="product_cote_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param ProductCote $productCote
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, ProductCote $productCote)
    {
        return
            $this
                ->productCoteHandler
                ->setEntity($productCote)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/product-cotes/{id}/delete", name="product_cote_delete",
     *     methods={"POST","GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param ProductCote $productCote
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ProductCote $productCote)
    {
        return  $this
            ->productCoteHandler
            ->setEntity($productCote)
            ->remove($request, $csrf);
    }
}
