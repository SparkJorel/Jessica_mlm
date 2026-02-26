<?php

namespace App\Services\ModelHandlers;

use App\Form\IndirectBonusMembershipType;
use App\Entity\IndirectBonusMembership;
use App\Repository\IndirectBonusMembershipRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndirectBonusMembershipHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(IndirectBonusMembershipType::class, $this->entity);
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
            return $this->submit(
                $request,
                'indirect_bonus_membership_list',
                'back/webcontroller/indirect_bonus_membership/new.html.twig',
                'success',
                'valeur créée'
            );
        } else {
            return $this->submit(
                $request,
                'indirect_bonus_membership_list',
                'back/webcontroller/indirect_bonus_membership/new.html.twig',
                'success',
                'valeur modifiée'
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
        if ($this->isTokenValid($csrf, $request, '_jtwc_indirect_bonus_token', 'jtwc_indirect_bonus-delete')) {
            return $this->processRemovEntity('indirect_bonus_membership_list', 'info', 'valeur supprimée');
        } else {
            return $this->redirectAfterSubmit('indirect_bonus_membership_list', 'danger', 'A problem occured when processing the request!!');
        }
    }


    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        return $this
            ->getEntityView('back/webcontroller/indirect_bonus_membership/show.html.twig');
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
        $indirectBonusMemberships = $this->getIndirectBonusMemberships();

        $indirectBonusView
                        = $this
                            ->twig
                            ->render(
                                'back/webcontroller/indirect_bonus_membership/list.html.twig',
                                [
                                    'indirectBonusMemberships' => $indirectBonusMemberships
                                ]
                            );

        return new Response($indirectBonusView);
    }

    protected function getIndirectBonusMemberships(): ?array
    {
        /**
         * @var IndirectBonusMembershipRepository $repository
         */
        $repository = $this
                        ->manager
                        ->getRepository(get_class($this->entity));

        /** @var IndirectBonusMembership[]|null */
        $indirectBonusMemberships = $repository->findAll();

        if (!$indirectBonusMemberships) {
            return null;
        }

        $data = [];

        /**
         * @var IndirectBonusMembership $indirectBonusMembership
         */
        foreach ($indirectBonusMemberships as $indirectBonusMembership) {
            $data[$indirectBonusMembership->getMembership()->getCode()][] = ['lvl' => $indirectBonusMembership->getLvl(), 'value' => $indirectBonusMembership->getValue(), 'id' => $indirectBonusMembership->getId()];
        }

        return $data;
    }
}
