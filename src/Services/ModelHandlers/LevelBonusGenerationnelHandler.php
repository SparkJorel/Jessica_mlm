<?php

namespace App\Services\ModelHandlers;

use App\Form\LevelBonusGenerationnelType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LevelBonusGenerationnelHandler extends ModelSingleEntityAbstract implements ModelInterface
{
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
                'level_bonus_generationnel_list',
                'back/webcontroller/level_bonus_generationnel/new.html.twig',
                'success',
                'Level du bonus générationnel créé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'level_bonus_generationnel_list',
                'back/webcontroller/level_bonus_generationnel/new.html.twig',
                'success',
                'Level du bonus générationnel modifié'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_level_bonus_generationnel_token', 'jtwc_level_bonus_generationnel-delete')) {
            return $this->processRemovEntity('level_bonus_generationnel_list', 'info', 'Level bonus generationnel supprimé');
        } else {
            return $this->redirectAfterSubmit('level_bonus_generationnel_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    public function show()
    {
        // TODO: Implement show() method.
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
        $levelBGs = $this->getEntities();
        $levelBGView = $this
            ->twig
            ->render(
                'back/webcontroller/level_bonus_generationnel/list.html.twig',
                [
                    'levelBGs' => $levelBGs
                ]
            );

        return new Response($levelBGView);
    }

    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this->formFactory->create(LevelBonusGenerationnelType::class, $this->entity);
    }
}
