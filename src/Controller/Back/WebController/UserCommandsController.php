<?php

namespace App\Controller\Back\WebController;

use App\Entity\UserCommands;
use DateTime;
use Exception;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Manager\CartUserLoggedManager;
use App\Services\ModelHandlers\UserCommandsHandler;
use App\Services\SaveUserCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;

class UserCommandsController
{
    /**
     * @var UserCommandsHandler
     */
    private $userCommandsHandler;

    /**
     * @var EntityManagerInterface
     */
    private $manager;


    public function __construct(
        UserCommandsHandler $userCommandsHandler,
        EntityManagerInterface $manager
    ) {
        $this->userCommandsHandler = $userCommandsHandler;
        $this->manager = $manager;
    }

    /**
     * @Route("/commands", name="user_command_list", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(Request $request)
    {
        return
            $this
                ->userCommandsHandler
                ->setEntity((new UserCommands()))
                ->commandsCycle($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("user-commands/all", name="list_all_user_commands", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function listAll(Request $request)
    {
        return
            $this
                ->userCommandsHandler
                ->setEntity((new UserCommands()))
                ->allCommandsCycle($request);
    }

    /**
     * @Route("commands/personal", name="user_personal_command_list", options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function listPersonal(Request $request)
    {
        return
            $this
                ->userCommandsHandler
                ->setEntity((new UserCommands()))
                ->commandsCycle($request, false);
    }

    /**
	 * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/commands/new", name="user_command_new", methods={"GET","POST"})
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create()
    {
        return  $this->userCommandsHandler->selectProduct();
    }

    /**
     * @Route("/cart/view", name="user_cart_create", methods={"GET", "POST"}, options={"expose"=true})
     * @param Request $request
     * @param CartUserLoggedManager $cartManager
     * @return Response
     */
    public function index(Request $request, CartUserLoggedManager $cartManager): Response
    {
        return $this->userCommandsHandler->createCart($request, $cartManager);
    }

    /**
     * @Route("/commands/{id}", name="user_command_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param UserCommands $userCommand
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(UserCommands $userCommand)
    {
        return $this->userCommandsHandler->setEntity($userCommand)->show();
    }

    /**
     * @Route("/commands/{id}/edit", name="user_command_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param UserCommands $userCommand
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(UserCommands $userCommand)
    {
        return
            $this
                ->userCommandsHandler
                ->setEntity($userCommand)
                ->updateCommandProduct();
    }

    /**
     * @Route("/commands/save", name="save_commands",
     *     methods={"GET","POST"}, options={"expose"=true}
     * )
     * @param Request $request
     * @param RouterInterface $router
     * @return RedirectResponse
     * @throws Exception
     */
    public function save(Request $request, SaveUserCommand $saveUserCommand, RouterInterface $router)
    {
        $command_id = $request->request->get('command_id');
        $commands = $request->request->get('commands');
        $user_query = $request->request->get('user');
        $date_op = $request->request->get('date_op');

        if ($command_id) {
            /**
             * @var UserCommands $userCommand
             */
            $userCommand = $this->manager
                                ->getRepository(UserCommands::class)
                                ->find((int)$command_id);
        } else {
            $userCommand = (new UserCommands())
                                        ->setMotif("Achat");
        }

        $saveUserCommand
                ->setUsername($user_query)
                ->setCommands($commands);

        if ($date_op) {
            $date_op = new DateTime($date_op, new DateTimeZone("Africa/Douala"));
            $saveUserCommand->setDateOp($date_op);
        }

        if (!$command_id) {
            $saveUserCommand->saveCommand($userCommand);
        } else {
            $saveUserCommand->editCommand($userCommand);
        }

        return new RedirectResponse($router->generate('user_command_list'));
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/commands/{id}/delete", name="user_command_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param UserCommands $userCommand
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, UserCommands $userCommand)
    {
        return  $this
            ->userCommandsHandler
            ->setEntity($userCommand)
            ->remove($request, $csrf);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/commands/{id}/delivered", name="user_command_delivered",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param UserCommands $userCommand
     * @return RedirectResponse
     */
    public function delivered(UserCommands $userCommand)
    {
        return $this->userCommandsHandler->setEntity($userCommand)->setDeliveredToTrue();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/commands/{id}/paid", name="user_command_paid",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param UserCommands $userCommand
     * @return RedirectResponse
     */
    public function paid(UserCommands $userCommand)
    {
        return $this->userCommandsHandler->setEntity($userCommand)->setPaidToTrue();
    }
}
