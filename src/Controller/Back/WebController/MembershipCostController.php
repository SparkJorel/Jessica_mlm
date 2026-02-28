<?php

namespace App\Controller\Back\WebController;

use App\Entity\MembershipCost;
use App\Services\ModelHandlers\MembershipCostHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipCostController
{
    /**
     * @var MembershipCostHandler
     */
    private $mbshipCostHandler;

    public function __construct(MembershipCostHandler $mbshipCostHandler)
    {
        $this->mbshipCostHandler = $mbshipCostHandler;
    }

    #[Route('/membership-costs', name: 'membership_cost_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity((new MembershipCost()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-costs/{id}', name: 'membership_cost_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(MembershipCost $cost)
    {
        return $this->mbshipCostHandler->setEntity($cost)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-costs/new', name: 'membership_cost_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity((new MembershipCost()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-costs/{id}/edit', name: 'membership_cost_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, MembershipCost $cost)
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity($cost)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-costs/{id}/delete', name: 'membership_cost_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipCost $cost)
    {
        return  $this
                    ->mbshipCostHandler
                    ->setEntity($cost)
                    ->remove($request, $csrf);
    }
}
