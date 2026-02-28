<?php

namespace App\Controller\Back\WebController;

use App\Services\IndirectBonusService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IndirectBonusController
{
    /** @var IndirectBonusService */
    private $indirectBonusService;

    public function __construct(IndirectBonusService $indirectBonusService)
    {
        $this->indirectBonusService = $indirectBonusService;
    }

    #[Route('/user-indirect-bonus', name: 'user_indirect_bonus', methods: ['GET', 'POST'])]
    public function userIndirectBonus(Request $request): Response
    {
        return $this->indirectBonusService->viewUserIndirectBonus($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('network-indirect-bonus', name: 'network_indirect_bonus', methods: ['GET', 'POST'])]
    public function networkIndirectBonus(Request $request): Response
    {
        return $this->indirectBonusService->viewNetWorkIndirectBonus($request);
    }
}
