<?php

namespace App\Services\ModelHandlers;

use App\Form\MembershipSVType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipSVHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(MembershipSVType::class, $this->entity);
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
            return $this->submit(
                $request,
                'membership_sv_list',
                'back/webcontroller/membership_sv/new.html.twig',
                'success',
                'Membership SV created'
            );
        } else {
            return $this
                        ->submit(
                            $request,
                            'membership_sv_list',
                            'back/webcontroller/membership_sv/new.html.twig',
                            'success',
                            'Membership SV updated'
                        );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_sv_token', 'jtwc_membership_sv-delete')) {
            return $this->processRemovEntity('membership_sv_list', 'info', 'Membership SV deactivated');
        } else {
            return $this->redirectAfterSubmit('membership_sv_list', 'danger', 'A problem occured when processing the request!!');
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
        $mbship_svs = $this->getEntities();
        $mbship_svsView = $this
                            ->twig
                            ->render(
                                'back/webcontroller/membership_sv/list.html.twig',
                                [
                                    'mbship_svs' => $mbship_svs
                                ]
                            );

        return new Response($mbship_svsView);
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
        return $this->getEntityView('back/webcontroller/membership_sv/show.html.twig');
    }
}
