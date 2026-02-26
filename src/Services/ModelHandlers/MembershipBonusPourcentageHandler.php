<?php

namespace App\Services\ModelHandlers;

use App\Form\MembershipBonusPourcentageType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipBonusPourcentageHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    /**
     * @return FormInterface
     */
    protected function createForm(): FormInterface
    {
        return $this
                ->formFactory
                ->create(MembershipBonusPourcentageType::class, $this->entity);
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
                            'membership_bp_list',
                            'back/webcontroller/membership_bp/new.html.twig',
                            'success',
                            'Membership Bonus Pourcentage created'
                        );
        } else {
            return $this
                        ->submit(
                            $request,
                            'membership_bp_list',
                            'back/webcontroller/membership_bp/new.html.twig',
                            'success',
                            'Membership Bonus Pourcentage updated'
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
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_bp_token', 'jtwc_membership_bp-delete')) {
            return $this->processRemovEntity('membership_bp_list', 'info', 'Membership Bonus Pourcentage deactivated');
        } else {
            return $this->redirectAfterSubmit('membership_bp_list', 'danger', 'A problem occured when processing the request!!');
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
        $membership_bps = $this->getEntities();
        $membership_bpsView = $this
                                ->twig
                                ->render(
                                    'back/webcontroller/membership_bp/list.html.twig',
                                    [
                                        'membership_bps' => $membership_bps
                                    ]
                                );

        return new Response($membership_bpsView);
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
        return $this->getEntityView('back/webcontroller/membership_bp/show.html.twig');
    }
}
