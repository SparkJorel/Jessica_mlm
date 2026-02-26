<?php

namespace App\Controller\Back\WebController;

use App\Entity\Membership;
use App\Services\ModelHandlers\MembershipHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipController
{
    /**
     * @var MembershipHandler
     */
    private $membershipHandler;

    public function __construct(MembershipHandler $membershipHandler)
    {
        $this->membershipHandler = $membershipHandler;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/memberships", name="membership_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->membershipHandler->setEntity((new Membership()))->list();
    }

    /**
     * @Route("/packs/view/all", name="packs_view_all", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function viewAllPacks()
    {
        return $this->membershipHandler->setEntity((new Membership()))->viewAllPacks();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership/{id}", name="membership_one", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param Membership $membership
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Membership $membership)
    {
        return $this->membershipHandler->setEntity($membership)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/memberships/new", name="membership_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return $this
                    ->membershipHandler
                    ->setEntity(new Membership())
                    ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/memberships/{id}/edit", name="membership_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param Membership $membership
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, Membership $membership)
    {
        return
            $this
                ->membershipHandler
                ->setEntity($membership)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/memberships/{id}/delete", name="membership_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param Membership $membership
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Membership $membership)
    {
        return
            $this
                ->membershipHandler
                ->setEntity($membership)
                ->remove($request, $csrf);
    }
}
