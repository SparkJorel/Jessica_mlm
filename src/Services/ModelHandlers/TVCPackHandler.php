<?php

namespace App\Services\ModelHandlers;

use App\Form\TCVPackType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TVCPackHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'membership_tvc_list',
                'back/webcontroller/tvc_pack/new.html.twig',
                'success',
                'La côte du produit a été créée'
            );
        } else {
            return $this->submit(
                $request,
                'membership_tvc_list',
                'back/webcontroller/tvc_pack/new.html.twig',
                'success',
                'La côte du produit a été modifiée'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_tvc_pack_token', 'jtwc_tvc_pack-delete')) {
            return $this->processRemovEntity('membership_tvc_list', 'info', 'Volume total commission créé');
        } else {
            return $this->redirectAfterSubmit('membership_tvc_list', 'danger', 'A problem occured when processing the request!!');
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
        return new Response(
            $this->twig->render(
                'back/webcontroller/product_cote/show.html.twig',
                ['entity' => $this->entity]
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
        $tvcs = $this->getEntities();
        $tvcView = $this
            ->twig
            ->render(
                'back/webcontroller/tvc_pack/list.html.twig',
                [
                    'tvcs' => $tvcs
                ]
            );

        return new Response($tvcView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(TCVPackType::class, $this->entity);
    }
}
