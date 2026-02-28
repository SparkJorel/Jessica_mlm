<?php

namespace App\Controller\Back\WebController;

use App\Entity\Membership;
use App\Services\ModelHandlers\MembershipHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/memberships', name: 'membership_list', methods: ['GET'])]
    public function list()
    {
        return $this->membershipHandler->setEntity((new Membership()))->list();
    }

    #[Route('/packs/view/all', name: 'packs_view_all', methods: ['GET'])]
    public function viewAllPacks()
    {
        return $this->membershipHandler->setEntity((new Membership()))->viewAllPacks();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership/{id}', name: 'membership_one', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Membership $membership)
    {
        return $this->membershipHandler->setEntity($membership)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/memberships/new', name: 'membership_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this
                    ->membershipHandler
                    ->setEntity(new Membership())
                    ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/memberships/{id}/edit', name: 'membership_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Membership $membership)
    {
        return
            $this
                ->membershipHandler
                ->setEntity($membership)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/memberships/{id}/delete', name: 'membership_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Membership $membership)
    {
        return
            $this
                ->membershipHandler
                ->setEntity($membership)
                ->remove($request, $csrf);
    }
}
