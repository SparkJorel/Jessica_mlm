<?php

namespace App\Controller\Back\WebController;

use App\Services\MonthlyTurnOver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/admin/turn-over/monthly", name="turn_over_monthly")
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function turnOver(Request $request)
    {
        $turnover = $this->monthlyTurnOver->turnOver($request);

        return new Response($turnover);
    }
}
