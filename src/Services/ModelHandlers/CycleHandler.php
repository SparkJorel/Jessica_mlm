<?php

namespace App\Services\ModelHandlers;

use App\Form\CycleType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CycleHandler extends ModelSingleEntityAbstract implements ModelInterface
{
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
                'cycle_list',
                'back/webcontroller/cycle/new.html.twig',
                'success',
                'Cycle créé'
            );
        } else {
            return $this->submit(
                $request,
                'cycle_list',
                'back/webcontroller/cycle/new.html.twig',
                'success',
                'Cycle mis à jour'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        return $this
            ->getEntityView('back/webcontroller/cycle/show.html.twig');
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        // TODO: Implement list() method.
        $cycles = $this->getEntities();
        $cycleView = $this
            ->twig
            ->render(
                'back/webcontroller/cycle/list.html.twig',
                [
                    'cycles' => $cycles
                ]
            );

        return new Response($cycleView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(CycleType::class, $this->entity);
    }
}
