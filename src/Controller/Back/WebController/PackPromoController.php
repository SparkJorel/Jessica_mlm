<?php

namespace App\Controller\Back\WebController;

use App\Entity\PackPromo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ModelHandlers\PackPromoHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PackPromoController
{
    /**
     * @var PackPromoHandler
     */
    private $packPromoHandler;

    public function __construct(PackPromoHandler $packPromoHandler)
    {
        $this->packPromoHandler = $packPromoHandler;
    }

    /**
     * @Route("/pack-promos", name="pack_promo_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->packPromoHandler
                ->setEntity((new PackPromo()))
                ->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/new", name="pack_promo_new", methods={"GET","POST"})
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
                ->packPromoHandler
                ->setEntity((new PackPromo()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/{id}", name="pack_promo_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param PackPromo $packPromo
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/{id}/edit", name="pack_promo_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param PackPromo $packPromo
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, PackPromo $packPromo)
    {
        return
            $this
                ->packPromoHandler
                ->setEntity($packPromo)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/{id}/delete", name="pack_promo_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param PackPromo $packPromo
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, PackPromo $packPromo)
    {
        return  $this
            ->packPromoHandler
            ->setEntity($packPromo)
            ->remove($request, $csrf);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/{id}/end", name="pack_promo_ended",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param PackPromo $packPromo
     * @return RedirectResponse
     */
    public function endPromo(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->endPromo();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-promos/{id}/start", name="pack_promo_started",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param PackPromo $packPromo
     * @return RedirectResponse
     */
    public function startPromo(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->startPromo();
    }
}
