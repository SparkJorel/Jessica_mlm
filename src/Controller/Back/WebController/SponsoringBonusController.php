<?php

namespace App\Controller\Back\WebController;

use App\Entity\Cycle;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use App\Repository\SponsoringBonusRepository;
use App\Repository\UserRepository;
use App\Services\PaidSponsoringBonus;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SponsoringBonusController
{
    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("bonus-sponsoring/paid", name="status_bonus_sponsoring_paid", options={"expose"=true})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function paidSponsoringBonus(Request $request, EntityManagerInterface $manager, RouterInterface $router)
    {
        $users = $request->request->get('users');
        $cycle = $request->request->get('cycle');

        $paidBonusSponsoring = new PaidSponsoringBonus($users, (int)$cycle);

        $paidBonusSponsoring->paid($manager);
        return new RedirectResponse($router->generate('status_bonus_sponsoring_paid'));
    }
}
