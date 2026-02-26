<?php

namespace App\Controller\Back\WebController;

use App\Entity\CompositionMembershipProductName;
use App\Services\ModelHandlers\CompositionMembershipProductNameHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/pack-name-composition", name="composition_membership_product_list", methods={"GET"})
     * @return Response
     */
    public function list()
    {
        $compositionMembershipProductName = new CompositionMembershipProductName();

        return $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-name-composition/create", name="composition_membership_product_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        $compositionMembershipProductName = new CompositionMembershipProductName();

        return  $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-name-composition/{id}/edit", name="composition_membership_product_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param CompositionMembershipProductName $compositionMembershipProductName
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, CompositionMembershipProductName $compositionMembershipProductName)
    {
        return  $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->save($request);
    }

    /**
     * @Route("/pack-name-composition/{id}/show", name="composition_membership_product_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param CompositionMembershipProductName $compositionMembershipProductName
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(CompositionMembershipProductName $compositionMembershipProductName)
    {
        return $this->compositionMembershipProductNameHandler->setEntity($compositionMembershipProductName)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/pack-name-composition/{id}/delete", name="composition_membership_product_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param CompositionMembershipProductName $compositionMembershipProductName
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, CompositionMembershipProductName $compositionMembershipProductName): RedirectResponse
    {
        return  $this
                    ->compositionMembershipProductNameHandler
                    ->setEntity($compositionMembershipProductName)
                    ->remove($request, $csrf);
    }
}
