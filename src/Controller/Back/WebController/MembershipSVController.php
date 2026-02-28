<?php

namespace App\Controller\Back\WebController;

use App\Entity\MembershipSV;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ModelHandlers\MembershipSVHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipSVController
{
    /**
     * @var MembershipSVHandler
     */
    private $membershipSVHandler;

    public function __construct(MembershipSVHandler $membershipSVHandler)
    {
        $this->membershipSVHandler = $membershipSVHandler;
    }

    #[Route('/membership-svs', name: 'membership_sv_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->membershipSVHandler
                ->setEntity((new MembershipSV()))
                ->list();
    }

    #[Route('/membership-svs/{id}', name: 'membership_sv_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(MembershipSV $sv)
    {
        return $this->membershipSVHandler->setEntity($sv)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-svs/new', name: 'membership_sv_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->membershipSVHandler
                ->setEntity((new MembershipSV()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-svs/{id}/edit', name: 'membership_sv_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, MembershipSV $sv)
    {
        return
            $this
                ->membershipSVHandler
                ->setEntity($sv)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-svs/{id}/delete', name: 'membership_sv_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipSV $sv)
    {
        return  $this
                    ->membershipSVHandler
                    ->setEntity($sv)
                    ->remove($request, $csrf);
    }
}
