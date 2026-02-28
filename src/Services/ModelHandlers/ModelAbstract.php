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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class ModelAbstract
{
    protected EntityManagerInterface $manager;
    protected FormFactoryInterface $formFactory;
    protected RouterInterface $router;
    protected Environment $twig;
    protected ?EntityInterface $entity = null;
    protected RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        RequestStack $requestStack
    ) {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
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
        $this->requestStack->getSession()->getFlashBag()->add($type, $message);
    }

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
        $entities = $this
                        ->manager
                        ->getRepository(get_class($this->entity))
                        ->findAll();

        return $entities;
    }

    protected function getEntityView(string $template)
    {
        return new Response(
            $this->twig->render(
                $template,
                ['entity' => $this->entity]
            )
        );
    }

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

    abstract protected function submit(
        Request $request,
        string $url_name,
        string $template,
        string $type,
        string $message,
        ?bool $mode = false
    ): Response;
}
