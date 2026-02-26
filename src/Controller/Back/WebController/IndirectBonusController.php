<?php

namespace App\Controller\Back\WebController;

use App\Services\IndirectBonusService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class IndirectBonusController
{
    /** @var IndirectBonusService */
    private $indirectBonusService;

    public function __construct(IndirectBonusService $indirectBonusService)
    {
        $this->indirectBonusService = $indirectBonusService;
    }

    /**
     * @Route("/user-indirect-bonus", name="user_indirect_bonus", methods={"GET", "POST"})
     * @return Response
     */
    public function userIndirectBonus(Request $request): Response
    {
        return $this->indirectBonusService->viewUserIndirectBonus($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("network-indirect-bonus", name="network_indirect_bonus", methods={"GET", "POST"})
     * @return Response
     */
    public function networkIndirectBonus(Request $request): Response
    {
        return $this->indirectBonusService->viewNetWorkIndirectBonus($request);
    }
}
