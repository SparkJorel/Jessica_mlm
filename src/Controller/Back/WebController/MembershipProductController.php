<?php

namespace App\Controller\Back\WebController;

use App\Entity\Membership;
use App\Entity\MembershipProduct;
use App\Services\ModelHandlers\MembershipProductHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipProductController
{
    /**
     * @var MembershipProductHandler $membershipProductHandler
     */
    private $membershipProductHandler;

    public function __construct(MembershipProductHandler $membershipProductHandler)
    {
        $this->membershipProductHandler = $membershipProductHandler;
    }

    #[Route('/membership-product/{code}/list', requirements: ['code' => '[a-zA-Z]+'], name: 'membership_product_list', methods: ['GET'])]
    public function list(Membership $membership)
    {
        $membershipProduct = (new MembershipProduct())
                                ->setMembership($membership);

        return $this->membershipProductHandler->setEntity($membershipProduct)->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-product/{code}/create', name: 'membership_product_new', requirements: ['code' => '[a-zA-Z]+'], methods: ['GET', 'POST'])]
    public function create(Request $request, Membership $membership)
    {
        $membershipProduct = (new MembershipProduct())
                                            ->setMembership($membership);

        return  $this->membershipProductHandler->setEntity($membershipProduct)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-product/{id}/edit', name: 'membership_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MembershipProduct $membershipProduct)
    {
        return  $this->membershipProductHandler->setEntity($membershipProduct)->save($request);
    }

    #[Route('/membership-product/{id}/show', name: 'membership_product_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(MembershipProduct $membershipProduct)
    {
        return $this->membershipProductHandler->setEntity($membershipProduct)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-product/{id}/delete', name: 'membership_product_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipProduct $membershipProduct)
    {
        return  $this
                    ->membershipProductHandler
                    ->setEntity($membershipProduct)
                    ->remove($request, $csrf);
    }
}
