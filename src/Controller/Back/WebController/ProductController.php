<?php

namespace App\Controller\Back\WebController;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ModelHandlers\ProductHandler;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ProductController
 * @package App\Controller\Back\WebController
 */
class ProductController
{
    /**
     * @var ProductHandler
     */
    private $productHandler;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ProductHandler $productHandler, EntityManagerInterface $manager)
    {
        $this->productHandler = $productHandler;
        $this->manager = $manager;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/products", name="product_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->productHandler
                ->setEntity((new Product()))
                ->list();
    }

    /**
     * @Route("/products/all", name="products_all", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function viewAllProducts()
    {
        $template = 'back/webcontroller/product/view_all_products.html.twig';

        return $this->productHandler->setEntity((new Product()))->list($template);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/products/new", name="product_new", methods={"GET","POST"})
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
                ->productHandler
                ->setEntity((new Product()))
                ->save($request)
            ;
    }

    /**
     * @Route("/products/{id}", name="product_show", methods={"GET", "POST"},
     * requirements={
     * "id": "\d+"
     * })
     * @param Product $product
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Product $product, Request $request)
    {
        return $this->productHandler->setEntity($product)->show($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/products/{id}/edit", name="product_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, Product $product)
    {
        return
                $this
                    ->productHandler
                    ->setEntity($product)
                    ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/products/{id}/delete", name="product_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param Product $product
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Product $product)
    {
        return  $this
                    ->productHandler
                    ->setEntity($product)
                    ->remove($request, $csrf);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/products/{id}/disabled", name="product_disabled", methods={"GET"},
     *     requirements={"id": "\d+"}
     * )
     * @param Product $product
     * @return RedirectResponse
     */
    public function disableProduct(Product $product)
    {
        return  $this
                    ->productHandler
                    ->setEntity($product)
                    ->disabled();
    }


    /**
     * @Route("/admin/product/autocomplete", methods={"GET", "POST"}, options={"expose"=true}, name="product_autocomplete")
     * @param Request $request
     * @return JsonResponse
     */
    public function autocompleProduct(Request $request)
    {
        $products = [];
        $term = trim(strip_tags($request->get('term')));

        /**
         * @var ProductRepository $repository
         */
        $repository = $this->manager->getRepository(Product::class);

        /** @var Product[]|null $prods */
        $prods = $repository->getAllProductsAndDistributorPrices($term);

        if ($prods) {
            /** @var Product $prod */
            foreach ($prods as $prod) {
                $products[] = $prod->getCode().' ('. $prod->getDistributorPrice() . ')';
            }
        }

        $response = new JsonResponse();
        $response->setData($products);

        return $response;
    }
}
