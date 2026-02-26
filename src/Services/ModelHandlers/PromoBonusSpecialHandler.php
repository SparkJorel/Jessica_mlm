<?php

namespace App\Services\ModelHandlers;

use App\Form\PromoBonusSpecialType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PromoBonusSpecialHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'promo_bonus_special_list',
                'back/webcontroller/promo_bonus_special/new.html.twig',
                'success',
                'Promotion bonus spécial créée'
            );
        } else {
            return $this->submit(
                $request,
                'promo_bonus_special_list',
                'back/webcontroller/promo_bonus_special/edit.html.twig',
                'success',
                'Promotion bonus spécial mise à jour'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_promo_bonus_special_token', 'jtwc_promo_bonus_special-delete')) {
            return $this->processRemovEntity('promo_bonus_special_list', 'info', 'Promotion bonus spécial désactivée');
        } else {
            return $this->redirectAfterSubmit('promo_bonus_special_list', 'danger', 'A problem occured when processing the request!!');
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
        return new Response(
            $this->twig->render(
                'back/webcontroller/promo_bonus_special/show.html.twig',
                [
                    'entity' => $this->entity
                ]
            )
        );
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        $promotions = $this->getEntities();
        $promotionsBonusSpecialView = $this
            ->twig
            ->render(
                'back/webcontroller/promo_bonus_special/list.html.twig',
                [
                    'promotions' => $promotions
                ]
            );

        return new Response($promotionsBonusSpecialView);
    }

    protected function createForm(): FormInterface
    {
        return $this
                    ->formFactory
                    ->create(PromoBonusSpecialType::class, $this->entity);
    }
}
