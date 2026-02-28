<?php

namespace App\Controller\Back\WebController;

use App\Entity\CompositionMembershipProductName;
use App\Services\ModelHandlers\CompositionMembershipProductNameHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CompositionMembershipProductNameController
{
    /**
     * @var CompositionMembershipProductNameHandler $compositionMembershipProductNameHandler
     */
    private $compositionMembershipProductNameHandler;

    public function __construct(CompositionMembershipProductNameHandler $handler)
    {
        $this->compositionMembershipProductNameHandler = $handler;
    }

    #[Route('/pack-name-composition', name: 'composition_membership_product_list', methods: ['GET'])]
    public function list()
    {
        $compositionMembershipProductName = new CompositionMembershipProductName();

        return $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-name-composition/create', name: 'composition_membership_product_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $compositionMembershipProductName = new CompositionMembershipProductName();

        return  $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-name-composition/{id}/edit', name: 'composition_membership_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CompositionMembershipProductName $compositionMembershipProductName)
    {
        return  $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->save($request);
    }

    #[Route('/pack-name-composition/{id}/show', name: 'composition_membership_product_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(CompositionMembershipProductName $compositionMembershipProductName)
    {
        return $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-name-composition/{id}/delete', name: 'composition_membership_product_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, CompositionMembershipProductName $compositionMembershipProductName): RedirectResponse
    {
        return  $this
                    ->compositionMembershipProductNameHandler
                    ->setEntity($compositionMembershipProductName)
                    ->remove($request, $csrf);
    }
}
