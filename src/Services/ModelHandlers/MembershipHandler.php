<?php

namespace App\Services\ModelHandlers;

use App\Entity\Membership;
use App\Form\MembershipType;
use App\Repository\MembershipRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(MembershipType::class, $this->entity);
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return string|RedirectResponse
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
                            'membership_list',
                            'back/webcontroller/membership/new.html.twig',
                            'success',
                            'Pack de souscription créé'
                        );
        } else {
            return $this
                        ->submit(
                            $request,
                            'membership_list',
                            'back/webcontroller/membership/new.html.twig',
                            'success',
                            'Pack de souscription mis à jour'
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
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_token', 'jtwc_membership-delete')) {
            return $this->processRemovEntity('membership_list', 'info', 'Membership deleted', true);
        } else {
            return $this->redirectAfterSubmit('membership_list', 'danger', 'A problem occured when processing the request!!');
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
        $memberships = $this->getEntities();
        $membershipsView = $this
                                ->twig
                                ->render(
                                    'back/webcontroller/membership/list.html.twig',
                                    [
                                        'memberships' => $memberships
                                    ]
                                );
        return new Response($membershipsView);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function viewAllPacks()
    {
        $memberships = $this->getEntities();
        $membershipsView = $this
                                ->twig
                                ->render(
                                    'back/webcontroller/membership/view_all_packs.html.twig',
                                    [
                                        'memberships' => $memberships
                                    ]
                                );
        return new Response($membershipsView);
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
            ->getEntityView(
                'back/webcontroller/membership/show.html.twig'
            );
    }

    /**
     * @return Membership
     */
    private function getMembership()
    {
        /**
         * @var Membership $membership
         */
        $membership = &$this->entity;

        return $membership;
    }

    /**
     * @return Membership[]|null
     */
    protected function getEntities()
    {

        /** @var MembershipRepository $repository */
        $repository = $this
                        ->manager
                        ->getRepository(get_class($this->entity));

        return $repository->findAll();
    }
}
