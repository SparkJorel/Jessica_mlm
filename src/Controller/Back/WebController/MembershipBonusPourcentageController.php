<?php

namespace App\Controller\Back\WebController;

use App\Entity\MembershipBonusPourcentage;
use App\Services\ModelHandlers\MembershipBonusPourcentageHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipBonusPourcentageController
{
    /**
     * @var MembershipBonusPourcentageHandler
     */
    private $membershipBPHandler;

    public function __construct(MembershipBonusPourcentageHandler $membershipBPHandler)
    {
        $this->membershipBPHandler = $membershipBPHandler;
    }

    #[Route('/membership-bonus', name: 'membership_bp_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->membershipBPHandler
                ->setEntity((new MembershipBonusPourcentage()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-bonus/{id}', name: 'membership_bp_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(MembershipBonusPourcentage $rb)
    {
        return $this->membershipBPHandler->setEntity($rb)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-bonus/new', name: 'membership_bp_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this->membershipBPHandler->setEntity((new MembershipBonusPourcentage()))
                    ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-bonus/{id}/edit', name: 'membership_bp_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, MembershipBonusPourcentage $rb)
    {
        return
            $this
                ->membershipBPHandler
                ->setEntity($rb)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-bonus/{id}/delete', name: 'membership_bp_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipBonusPourcentage $rb)
    {
        return  $this
                    ->membershipBPHandler
                    ->setEntity($rb)
                    ->remove($request, $csrf);
    }
}
