<?php

namespace App\Controller\Back\WebController;

use App\Entity\MembershipCost;
use App\Services\ModelHandlers\MembershipCostHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipCostController
{
    /**
     * @var MembershipCostHandler
     */
    private $mbshipCostHandler;

    public function __construct(MembershipCostHandler $mbshipCostHandler)
    {
        $this->mbshipCostHandler = $mbshipCostHandler;
    }

    /**
     * @Route("/membership-costs", name="membership_cost_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity((new MembershipCost()))
                ->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-costs/{id}", name="membership_cost_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param MembershipCost $cost
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(MembershipCost $cost)
    {
        return $this->mbshipCostHandler->setEntity($cost)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-costs/new", name="membership_cost_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity((new MembershipCost()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-costs/{id}/edit", name="membership_cost_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param MembershipCost $cost
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, MembershipCost $cost)
    {
        return
            $this
                ->mbshipCostHandler
                ->setEntity($cost)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/membership-costs/{id}/delete", name="membership_cost_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param MembershipCost $cost
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipCost $cost)
    {
        return  $this
                    ->mbshipCostHandler
                    ->setEntity($cost)
                    ->remove($request, $csrf);
    }
}
