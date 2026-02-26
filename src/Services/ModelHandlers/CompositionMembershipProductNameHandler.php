<?php

namespace App\Services\ModelHandlers;

use App\Form\CompositionMembershipProductNameType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CompositionMembershipProductNameHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'composition_membership_product_list',
                'back/webcontroller/composition_membership_product/new.html.twig',
                'success',
                'Nom de composition créé'
            );
        } else {
            return $this->submit(
                $request,
                'composition_membership_product_list',
                'back/webcontroller/composition_membership_product/new.html.twig',
                'success',
                'Nom de composition mis à jour'
            );
        }
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false) : RedirectResponse
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_pack_name_composition_token', 'jtwc_pack_name_composition-delete')) {
            return $this->processRemovEntity('composition_membership_product_list', 'info', 'Nom de composition de pack de subscription créé');
        } else {
            return $this->redirectAfterSubmit('composition_membership_product_list', 'danger', 'A problem occured when processing the request!!');
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
        return $this
            ->getEntityView('back/webcontroller/composition_membership_product/show.html.twig');
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
        $nameCompositions = $this->getEntities();
        $cycleView = $this
            ->twig
            ->render(
                'back/webcontroller/composition_membership_product/list.html.twig',
                [
                    'nameCompositions' => $nameCompositions
                ]
            );

        return new Response($cycleView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(CompositionMembershipProductNameType::class, $this->entity);
    }
}
