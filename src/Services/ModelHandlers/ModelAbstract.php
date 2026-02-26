<?php

namespace App\Services\ModelHandlers;

use App\AbstractModel\EntityInterface;
use App\Entity\UserCommandPackPromo;
use App\Entity\UserCommands;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class ModelAbstract
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var Environment
     */
    protected $twig;
    /**
     * @var EntityInterface
     */
    protected $entity;
    /**
     * @var FlashBagInterface
     */
    protected $session;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session
    )
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
        $this->session = $session;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    protected function validate(FormInterface $form)
    {
        return $form->isSubmitted() && $form->isValid();
    }

    protected function addFlash(string $type, string $message): void
    {
        $this->session->add($type, $message);
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @return RedirectResponse
     */
    protected function redirectAfterSubmit(string $url, string $type, string $message, array $params = null): RedirectResponse
    {
        $this->addFlash($type, $message);

        if ($params) {
            return new RedirectResponse($this->router->generate($url, $params));
        }

        return new RedirectResponse($this->router->generate($url));
    }

    /**
     * @return EntityInterface[]|null
     */
    protected function getEntities()
    {
        /**
         * @var EntityInterface[] $entities
         */
        $entities = $this
                        ->manager
                        ->getRepository(get_class($this->entity))
                        ->findAll();

        return $entities;
    }

    /**
     * @param string $template
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function getEntityView(string $template)
    {
        return new Response(
            $this->twig->render(
                $template,
                ['entity' => $this->entity]
            )
        );
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $message
     * @param bool|null $mode
     *
     * @return RedirectResponse
     */
    protected function processRemovEntity(string $url, string $type, string $message, ?bool $mode = false, array $params = null)
    {
        if ($mode) {
            $this->manager->remove($this->entity);
        }

        $this->manager->flush();

        if ($params) {
            return $this->redirectAfterSubmit($url, $type, $message, $params);
        } else {
            return $this->redirectAfterSubmit($url, $type, $message);
        }
    }

    protected function isTokenValid(CsrfTokenManagerInterface $csrf, Request $request, string $token, string $id)
    {
        $tokenSubmitted = $request->request->get($token);
        $hashedToken = new CsrfToken($id.$this->entity->getId(), $tokenSubmitted);
        return $csrf->isTokenValid($hashedToken);
    }

    protected function isTokenOk(CsrfTokenManagerInterface $csrf, Request $request, string $token, string $id)
    {
        $tokenSubmitted = $request->request->get($token);
        $hashedToken = new CsrfToken($id, $tokenSubmitted);
        return $csrf->isTokenValid($hashedToken);
    }


    protected function delivered(string $url_name, string $type, string $message)
    {
        if (($this->entity instanceof UserCommands ||
            $this->entity instanceof UserCommandPackPromo)) {
            $this->entity->setDelivered(true);
            $this->manager->flush();
        }

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    abstract protected function createForm(): FormInterface;

    /**
     * @param Request   $request
     * @param string    $url_name
     * @param string    $template
     * @param string    $type
     * @param string    $message
     * @param bool|null $mode
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    abstract protected function submit(
        Request $request,
        string $url_name,
        string $template,
        string $type,
        string $message,
        ?bool $mode = false
    ): Response;

    //protected abstract function hashPasswordAndSetUserUpline(User &$entity);
}
