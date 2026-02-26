<?php

namespace App\Controller\Back\WebController;

use App\Entity\TVCPack;
use App\Services\ModelHandlers\TVCPackHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TVCPackController
{
    /**
     * @var TVCPackHandler
     */
    private $tvcPackHandler;

    public function __construct(TVCPackHandler $tvcPackHandler)
    {
        $this->tvcPackHandler = $tvcPackHandler;
    }

    /**
     * @Route("/tvcs-pack", name="membership_tvc_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->tvcPackHandler
                ->setEntity((new TVCPack()))
                ->list();
    }

    /**
     * @Route("/tvcs-pack/new", name="membership_tvc_create", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return
            $this
                ->tvcPackHandler
                ->setEntity((new TVCPack()))
                ->save($request)
            ;
    }

    /**
     * @Route("/tvcs-pack/{id}", name="membership_tvc_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param TVCPack $tvcPack
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(TVCPack $tvcPack)
    {
        return $this->tvcPackHandler->setEntity($tvcPack)->show();
    }

    /**
     * @Route("/tvcs-pack/{id}/edit", name="membership_tvc_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param TVCPack $tvcPack
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, TVCPack $tvcPack)
    {
        return
            $this
                ->tvcPackHandler
                ->setEntity($tvcPack)
                ->save($request);
    }

    /**
     * @Route("/tvcs-pack/{id}/delete", name="membership_tvc_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param TVCPack $tvcPack
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, TVCPack $tvcPack)
    {
        return  $this
            ->tvcPackHandler
            ->setEntity($tvcPack)
            ->remove($request, $csrf);
    }
}
