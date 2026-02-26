<?php

namespace App\Controller\Back\WebController;

use Symfony\Component\Routing\Annotation\Route;
use App\Services\ModelHandlers\IndirectBonusMembershipHandler;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\IndirectBonusMembership;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndirectBonusMembershipController
{
    /**
     * @var IndirectBonusMembershipHandler
     */
    private $indirectBonusMembershipHandler;

    public function __construct(IndirectBonusMembershipHandler $handler)
    {
        $this->indirectBonusMembershipHandler = $handler;
    }

    /**
     * @Route("/indirect-bonus-memberships", name="indirect_bonus_membership_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function listIndirectBonusMembership()
    {
        $indirectBonusMembership = new IndirectBonusMembership();
        return $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/indirect-bonus-memberships/create", name="indirect_bonus_membership_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        $indirectBonusMembership = new IndirectBonusMembership();

        return  $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/indirect-bonus-membership/{id}/edit", name="indirect_bonus_membership_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param IndirectBonusMembership $indirectBonusMembership
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, IndirectBonusMembership $indirectBonusMembership)
    {
        return  $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->save($request);
    }

    /**
     * @Route("/indirect-bonus-membership/{id}/show", name="indirect_bonus_membership_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param IndirectBonusMembership $indirectBonusMembership
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(IndirectBonusMembership $indirectBonusMembership)
    {
        return $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/indirect-bonus-membership/{id}/delete", name="indirect_bonus_membership_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param IndirectBonusMembership $indirectBonusMembership
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, IndirectBonusMembership $indirectBonusMembership)
    {
        return  $this
                    ->indirectBonusMembershipHandler
                    ->setEntity($indirectBonusMembership)
                    ->remove($request, $csrf);
    }
}
