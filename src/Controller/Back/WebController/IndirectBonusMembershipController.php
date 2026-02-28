<?php

namespace App\Controller\Back\WebController;

use App\Entity\IndirectBonusMembership;
use App\Services\ModelHandlers\IndirectBonusMembershipHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('/indirect-bonus-memberships', name: 'indirect_bonus_membership_list', methods: ['GET'])]
    public function listIndirectBonusMembership()
    {
        $indirectBonusMembership = new IndirectBonusMembership();
        return $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-memberships/create', name: 'indirect_bonus_membership_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $indirectBonusMembership = new IndirectBonusMembership();

        return  $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-membership/{id}/edit', name: 'indirect_bonus_membership_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndirectBonusMembership $indirectBonusMembership)
    {
        return  $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->save($request);
    }

    #[Route('/indirect-bonus-membership/{id}/show', name: 'indirect_bonus_membership_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(IndirectBonusMembership $indirectBonusMembership)
    {
        return $this->indirectBonusMembershipHandler->setEntity($indirectBonusMembership)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/indirect-bonus-membership/{id}/delete', name: 'indirect_bonus_membership_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, IndirectBonusMembership $indirectBonusMembership)
    {
        return  $this
                    ->indirectBonusMembershipHandler
                    ->setEntity($indirectBonusMembership)
                    ->remove($request, $csrf);
    }
}
