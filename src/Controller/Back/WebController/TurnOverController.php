<?php

namespace App\Controller\Back\WebController;

use App\Services\MonthlyTurnOver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TurnOverController
{
    /**
     * @var MonthlyTurnOver
     */
    private $monthlyTurnOver;


    public function __construct(MonthlyTurnOver $monthlyTurnOver)
    {
        $this->monthlyTurnOver = $monthlyTurnOver;
    }

    #[Route('/admin/turn-over/monthly', name: 'turn_over_monthly')]
    #[IsGranted('ROLE_JTWC_ADMIN')]
    public function turnOver(Request $request)
    {
        $turnover = $this->monthlyTurnOver->turnOver($request);

        return new Response($turnover);
    }
}
