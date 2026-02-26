<?php

namespace App\Services\ModelHandlers;

use App\Form\MembershipCostType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipCostHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this
                    ->formFactory
                    ->create(
                        MembershipCostType::class,
                        $this->entity
                    );
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false)
    {
        if ($this->entity->isNew()) {
            return $this
                    ->submit(
                        $request,
                        'membership_cost_list',
                        'back/webcontroller/membership_cost/new.html.twig',
                        'success',
                        'Membership Cost created'
                    );
        } else {
            return $this->submit(
                $request,
                'membership_cost_list',
                'back/webcontroller/membership_cost/new.html.twig',
                'success',
                'Membership Cost updated'
            );
        }
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_cost_token', 'jtwc_membership_cost-delete')) {
            return $this->processRemovEntity('membership_cost_list', 'info', 'Membership Cost deactivated');
        } else {
            return $this->redirectAfterSubmit('membership_cost_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        // TODO: Implement list() method.
        $membership_costs = $this->getEntities();
        $membership_costsView = $this
            ->twig
            ->render(
                'back/webcontroller/membership_cost/list.html.twig',
                [
                    'mbship_costs' => $membership_costs
                ]
            );
        return new Response($membership_costsView);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        // TODO: Implement show() method.
        return $this
                ->getEntityView('back/webcontroller/membership_cost/show.html.twig');
    }
}
