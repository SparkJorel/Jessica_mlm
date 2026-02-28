<?php

namespace App\Controller\Back\WebController;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->productHandler
                ->setEntity((new Product()))
                ->list();
    }

    #[Route('/products/all', name: 'products_all', methods: ['GET'])]
    public function viewAllProducts()
    {
        $template = 'back/webcontroller/product/view_all_products.html.twig';

        return $this->productHandler->setEntity((new Product()))->list($template);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->productHandler
                ->setEntity((new Product()))
                ->save($request)
            ;
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(Product $product, Request $request)
    {
        return $this->productHandler->setEntity($product)->show($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Product $product)
    {
        return
                $this
                    ->productHandler
                    ->setEntity($product)
                    ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products/{id}/delete', name: 'product_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Product $product)
    {
        return  $this
                    ->productHandler
                    ->setEntity($product)
                    ->remove($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/products/{id}/disabled', name: 'product_disabled', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function disableProduct(Product $product)
    {
        return  $this
                    ->productHandler
                    ->setEntity($product)
                    ->disabled();
    }


    #[Route('/admin/product/autocomplete', methods: ['GET', 'POST'], options: ['expose' => true], name: 'product_autocomplete')]
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
