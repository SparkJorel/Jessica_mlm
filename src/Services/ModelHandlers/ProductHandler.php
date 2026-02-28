<?php

namespace App\Services\ModelHandlers;

use DateTimeZone;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\AddToCartType;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use App\Repository\ProductRepository;
use App\Manager\CartUserLoggedManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    /** @var CartUserLoggedManager */
    private $cartManager;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        RequestStack $requestStack,
        CartUserLoggedManager $cartManager
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $requestStack);
        $this->cartManager = $cartManager;
    }
    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this->formFactory->create(ProductType::class, $this->entity);
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
        if ($this->entity->isNew()) {
            return $this->submit(
                $request,
                'product_list',
                'back/webcontroller/product/new.html.twig',
                'success',
                'Produit créé'
            );
        } else {
            return $this->submit(
                $request,
                'product_list',
                'back/webcontroller/product/edit.html.twig',
                'success',
                'Produit mis à jour'
            );
        }
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_product_token', 'jtwc_product-delete')) {
            return $this->processRemovEntity('product_list', 'info', 'Product deactivated');
        } else {
            return $this->redirectAfterSubmit('product_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @return RedirectResponse
     */
    public function disabled()
    {
        return $this->disabledProduct('product_list', 'info', 'Product deactivated');
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(string $template = 'back/webcontroller/product/list.html.twig')
    {
        // TODO: Implement list() method.
        $products = $this->getEntities();
        $productView = $this
                            ->twig
                            ->render(
                                $template,
                                [
                                   'products' => $products
                                ]
                            );

        return new Response($productView);
    }


    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Request $request = null)
    {
        $form = $this->formFactory->create(AddToCartType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandProducts = $form->getData();
            $commandProducts->setProduct($this->getProduct());

            $cart = $this->cartManager->getCurrentCart();

            $cart
                ->addProduct($commandProducts)
                ->setDateCommandUpdate(new DateTimeImmutable("now", new DateTimeZone("Africa/Douala")));

            $this->cartManager->save($cart);

            return $this->redirectAfterSubmit('product_show', 'sucess', 'Merci d\'avoir ajouté un produit au panier', ['id' => $this->getProduct()->getId()]);
        }

        return new Response(
            $this->twig->render(
                'back/webcontroller/product/show.html.twig',
                [
                    'entity' => $this->entity,
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @return Product
     */
    private function getProduct()
    {
        /**
         * @var Product $product
         */
        $product = &$this->entity;
        return $product;
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $message
     *
     * @return RedirectResponse
     */
    protected function disabledProduct(string $url, string $type, string $message): RedirectResponse
    {
        $this->getProduct()->setStatus(false);

        $this->manager->flush();

        return $this->redirectAfterSubmit($url, $type, $message);
    }

    /**
     * @return Product[]|null
     */
    protected function getEntities()
    {

        /**
         * @var ProductRepository $productRepos
         */
        $productRepos = $this
                        ->manager
                        ->getRepository(get_class($this->entity));

        return $productRepos->findBy(['status' => true]);
    }

    /**
     * @param string $template
     * @param FormInterface $form
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function renderFormView(string $template, FormInterface $form)
    {
        return new Response(
            $this->twig->render($template, [
                'form' => $form->createView(),
                'name' => $this->getProduct()->getName()
            ])
        );
    }
}
