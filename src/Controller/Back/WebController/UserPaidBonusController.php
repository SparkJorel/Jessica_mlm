<?php

namespace App\Controller\Back\WebController;

use App\Services\PaymentBonus;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class UserPaidBonusController
{
    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/users/bonus/paid", name="users_bonus_paid", options={"expose"=true})
     * @param Request $request
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function updateStatusPaidBonus(
        Request $request,
        RouterInterface $router,
        PaymentBonus $paymentBonus
    )
    {
        $users = $request->get('users');
        $reason = $request->get('reason');
        $month = $request->get('month');
        $year = $request->get('year');
        $endedAt = $request->get('endedAt');
        $startedAt = $request->get('startedAt');

        $url = $request->request->get('url');

        $paymentBonus
            ->setUsers($users)
            ->setYear($year)
            ->setMonth($month)
            ->setReason($reason)
            ->setEndedAt($endedAt)
            ->setStartedAt($startedAt);

        $paymentBonus->savePayment();

        return new RedirectResponse($router->generate($url));
    }
}
