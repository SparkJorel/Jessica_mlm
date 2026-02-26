<?php

namespace App\Controller\Back\WebController;

use App\Entity\MembershipSV;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ModelHandlers\MembershipSVHandler;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipSVController
{
    /**
     * @var MembershipSVHandler
     */
    private $membershipSVHandler;

    public function __construct(MembershipSVHandler $membershipSVHandler)
    {
        $this->membershipSVHandler = $membershipSVHandler;
    }

    /**
     * @Route("/membership-svs", name="membership_sv_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->membershipSVHandler
                ->setEntity((new MembershipSV()))
                ->list();
    }

    /**
     * @Route("/membership-svs/{id}", name="membership_sv_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param MembershipSV $sv
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(MembershipSV $sv)
    {
        return $this->membershipSVHandler->setEntity($sv)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-svs/new", name="membership_sv_new", methods={"GET","POST"})
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
                ->membershipSVHandler
                ->setEntity((new MembershipSV()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-svs/{id}/edit", name="membership_sv_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param MembershipSV $sv
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, MembershipSV $sv)
    {
        return
            $this
                ->membershipSVHandler
                ->setEntity($sv)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-svs/{id}/delete", name="membership_sv_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param MembershipSV $sv
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipSV $sv)
    {
        return  $this
                    ->membershipSVHandler
                    ->setEntity($sv)
                    ->remove($request, $csrf);
    }
}
