<?php

namespace App\Services\ModelHandlers;

use DateTimeZone;
use App\Entity\User;
use App\Entity\Cycle;
use Twig\Environment;
use App\Form\CartType;
use App\Entity\FiltreCycle;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use App\Entity\UserCommands;
use Twig\Error\RuntimeError;
use App\Entity\FiltreProduct;
use App\Form\FiltreCycleType;
use App\Entity\CommandProducts;
use App\Form\FiltreProductType;
use App\AbstractModel\EntityInterface;
use App\Manager\CartUserLoggedManager;
use App\Entity\ProductDistributorPrice;
use App\Services\ExtractSVFromCommands;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use App\Repository\UserCommandsRepository;
use App\Repository\CycleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use App\Repository\ProductDistributorPriceRepository;
use DateTime;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserCommandsHandler extends ModelCollectionEntityAbstract implements ModelInterface
{
    /**
     * @var ExtractSVFromCommands
     */
    private $extractSVFromCommands;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /** @var SessionInterface */
    private $sessionAttributes;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session,
        SessionInterface $sessionAttributes,
        ExtractSVFromCommands $extractSVFromCommands,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($manager, $formFactory, $router, $twig, $session);
        $this->extractSVFromCommands = $extractSVFromCommands;
        $this->tokenStorage = $tokenStorage;
        $this->sessionAttributes = $sessionAttributes;
    }

    protected function createForm(): FormInterface
    {
        $filtre = new FiltreProduct();
        return $this->formFactory->create(FiltreProductType::class, $filtre);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function selectProduct()
    {
        $form = $this->createForm();
        return new Response(
            $this->twig->render('back/webcontroller/user_commands/new.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @param Request $request
     * @param CartUserLoggedManager $cartManager
     * @return Response
     */
    public function createCart(Request $request, CartUserLoggedManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();

        //dd($cart);

        /** @var FormInterface $form */
        $form = $this->formFactory->create(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setDateCommandUpdate(new \DateTimeImmutable("now", new DateTimeZone("Africa/Douala")));

            $cartManager->save($cart);

            /** @var FormInterface $formSave */
            $formSave = $form->get('save');

            if ($formSave->isClicked()) {
                $this->sessionAttributes->remove('cart_id');
            }

            return $this->redirectAfterSubmit('user_cart_create', 'success', 'Commande sauvegardée avec succès !!!');
        }

        return new Response(
            $this->twig->render('back/webcontroller/user_commands/cart.html.twig', [
                'cart' => $cart,
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updateCommandProduct()
    {
        $form = $this->createForm();
        $infosCommandes = $this->getProductDistributorPrice($this->getEntity());

        return new Response(
            $this->twig->render('back/webcontroller/user_commands/edit_command.html.twig', [
                'form' => $form->createView(),
                'commands' => $infosCommandes[0],
                'totalCommande' => $infosCommandes[1],
                'id_commande' => $this->getEntity()->getId(),
                'user' => $this->getEntity()->getUser()->getFullname() . ' (' . $this->getEntity()->getUser()->getUsername() .')',
            ])
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
        if ($this->entity->isNew()) {
            return $this
                ->submit(
                    $request,
                    'user_command_list',
                    'back/webcontroller/user_commands/new.html.twig',
                    'success',
                    'The order was saved successfully!!!'
                );
        } else {
            return $this
                ->submit(
                    $request,
                    'user_command_list',
                    'back/webcontroller/user_commands/new.html.twig',
                    'success',
                    'Editing order success!!!'
                );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = true)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_user_commands_token', 'jtwc_user_commands-delete')) {
            return $this->processRemovEntity('user_command_list', 'info', 'User\'s order deleted', $mode);
        } else {
            return $this->redirectAfterSubmit('user_command_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        /**
         * @var Collection|CommandProducts[] $products
         */
        $products = $this->getEntity()->getProducts();

        return new Response(
            $this->twig->render(
                'back/webcontroller/user_commands/show.html.twig',
                [
                    'products' => $products->toArray()
                ]
            )
        );
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        $userCommands = $this->getEntities();
        $userCmdView = $this
                                            ->twig
                                            ->render(
                                                'back/webcontroller/user_commands/list.html.twig',
                                                [
                                                    'userCommands' => $userCommands
                                                ]
                                            );

        return new Response($userCmdView);
    }

    /**
     * @param Request $request
     * @param bool $network
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function commandsCycle(Request $request, bool $network = true)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $cycle = null;
        $form = $this->createFormCycle();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $cycleRepository  */
            $cycleRepository = $this->manager->getRepository(Cycle::class);
            $cycle = $cycleRepository->getLastCycle();
        }

        /**
         * @var UserCommandsRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommands::class);

        if ($network) {
            $userCommands = $repository->getAllNetworkCycleCommands($cycle, $user->getLft(), $user->getRgt());
        } else {
            $userCommands = $repository->getAllCommandsByCycle($cycle, $user);
        }

        //dd($userCommands);

        $svUserCommands = $this->extractSVFromCommands
                                                   ->getSVFromCommands($userCommands);

        $svAchatUserCommands = $this->extractSVFromCommands
                                                   ->getSVAchatPersonnel($userCommands);

        $userCmdView = $this
                            ->twig
                            ->render(
                                'back/webcontroller/user_commands/list.html.twig',
                                [
                                    'form' => $form->createView(),
                                    'userCommands' => $userCommands,
                                    'svUserCommands' => $svUserCommands,
                                    'svAchatUserCommands' => $svAchatUserCommands,
                                    'title' => $network ? 'Les achats de mon réseau' : 'Mes achats personnels',
                                ]
                            );

        return new Response($userCmdView);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function allCommandsCycle(Request $request)
    {

        $cycle = null;
        $form = $this->createFormCycle();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $cycleRepository  */
            $cycleRepository = $this->manager->getRepository(Cycle::class);
            $cycle = $cycleRepository->getLastCycle();
        }

        /**
         * @var UserCommandsRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommands::class);

        $userCommands = $repository->getAllCommandsByCycle($cycle);

        //dd($userCommands);

        $svUserCommands = $this->extractSVFromCommands
                                                   ->getSVFromCommands($userCommands);

        $svAchatUserCommands = $this->extractSVFromCommands
                                                   ->getSVAchatPersonnel($userCommands);

        $userCmdView = $this
                            ->twig
                            ->render(
                                'back/webcontroller/user_commands/list.html.twig',
                                [
                                    'form' => $form->createView(),
                                    'userCommands' => $userCommands,
                                    'svUserCommands' => $svUserCommands,
                                    'svAchatUserCommands' => $svAchatUserCommands,
                                    'title' => 'Les achats du réseau',
                                ]
                            );

        return new Response($userCmdView);
    }


    /**
     * @return EntityInterface[]|null
     */
    protected function getEntities()
    {
        /** @var UserCommandsRepository $repository  */
        $repository = $this->manager->getRepository(get_class($this->entity));

        /** @var EntityInterface[] $entities  */
        $entities = $repository->findAll();
    
        return $entities;
    }


    protected function delivered(string $url_name, string $type, string $message)
    {
        $this->getEntity()->setDelivered(true);
        $this->manager->flush();
        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    protected function paid(string $url_name, string $type, string $message)
    {
        $this->getEntity()->setPaid(true);
	  	$this->getEntity()->setStatus(UserCommands::STATUS_ORDERED);
	  //$this->getEntity()->setDateCommand(new DateTime("now", new DateTimeZone("Africa/Douala")));
        $this->manager->flush();
        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    public function setDeliveredToTrue()
    {
        return $this->delivered('user_command_list', 'info', 'The order\'s status delivered has been successfully updated');
    }

    public function setPaidToTrue()
    {
        return $this->paid('user_command_list', 'info', 'The order\'s status paid has been successfully updated');
    }

    /**
     * @return FormInterface
     */
    private function createFormCycle(): FormInterface
    {
        $filtreCycle = new FiltreCycle();
        return $this->formFactory->create(FiltreCycleType::class, $filtreCycle);
    }

    /**
     * @param UserCommands $commands
     * @return array|bool
     */
    private function getProductDistributorPrice(UserCommands $commands)
    {
        $informations = [];
        $totalCommande = 0;
        $commandInfos = [];

        /**
         * @var ProductDistributorPriceRepository $repository
         */
        $repository = $this->manager->getRepository(ProductDistributorPrice::class);

        $products = $commands->getProducts();

        /**
         * @var integer[] $product_ids
         */
        $product_ids = $products->map(function (CommandProducts $p) {
            return $p->getProduct()->getId();
        });

        /**
         * @var ProductDistributorPrice[]|null
         */
        $prices = $repository->getPriceDistributorOfProducts($product_ids);

        foreach ($products as $product) {
            $commandInfos[] = [$product->getProduct(), $product->getQuantity()];
        }

        $commandInfosComplete = array_map(function ($p) use ($prices, $totalCommande) {
            foreach ($prices as $price) {
                if ($price->getProduct()->getId() === $p[0]->getId()) {
                    $p[] = $price->getPrice();
                    $p[] = $p[1] * $price->getPrice();
                    $totalCommande += $p[1] * $price->getPrice();
                }
            }

            return $p;
        }, $commandInfos);

        $totalCommande = array_reduce(array_map(function ($c) {
            return $c[3];
        }, $commandInfosComplete), function ($somme, $pu) {
            $somme += $pu;
            return $somme;
        }, 0);

        $informations[] = $commandInfosComplete;
        $informations[] = $totalCommande;

        return $informations;
    }

    /**
     * @return UserCommands
     */
    private function getEntity()
    {
        /**
         * @var UserCommands $userCommands
         */
        $userCommands = &$this->entity;
        return $userCommands;
    }
}
