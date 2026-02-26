<?php

namespace App\Services\ModelHandlers;

use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class ModelSingleEntityAbstract extends ModelAbstract
{
    abstract protected function createForm(): FormInterface;

    /**
     * @param Request $request
     * @param string $url_name
     * @param string $template
     * @param string $type
     * @param string $message
     * @param bool|null $mode
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function submit(Request $request, string $url_name, string $template, string $type, string $message, ?bool $mode = false, array $params = null): Response
    {
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($this->validate($form)) {
            if ($params) {
                return $this->saveEntity($url_name, $type, $message, $params);
            } else {
                return $this->saveEntity($url_name, $type, $message);
            }
        } else {
            return $this->renderFormView($template, $form);
        }
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @return RedirectResponse
     */
    protected function saveEntity(string $url_name, string $type, string $message, array $params = null)
    {
        if ($this->entity->isNew()) {
            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        if ($params) {
            return $this->redirectAfterSubmit($url_name, $type, $message, $params);
        } else {
            return $this->redirectAfterSubmit($url_name, $type, $message);
        }
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
                'form' => $form->createView()
            ])
        );
    }
}
