<?php

namespace App\Services\ModelHandlers;

use App\Entity\PackPromo;
use App\Form\PackPromoType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PackPromoHandler extends ModelCollectionEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(PackPromoType::class, $this->entity);
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
                    'pack_promo_list',
                    'back/webcontroller/pack_promo/new.html.twig',
                    'success',
                    'Le Pack (promo) consommateur  a été enregistré avec succès!!!'
                );
        } else {
            return $this
                ->submit(
                    $request,
                    'pack_promo_list',
                    'back/webcontroller/pack_promo/new.html.twig',
                    'success',
                    'Le Pack (promo) consommateur  a été modifié avec succès!!!'
                );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = true)
    {
        if ($this->isTokenValid($csrf, $request, 'jtwc_promo_pack_token', 'jtwc_promo_pack-delete')) {
            return $this->processRemovEntity('pack_promo_list', 'info', 'Pack promo deleted', $mode);
        } else {
            return $this->redirectAfterSubmit('pack_promo_list', 'danger', 'A problem occured when processing the request!!');
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
        /**
         * @var $entity PackPromo
         */
        $entity = $this->entity;

        return new Response(
            $this->twig->render(
                'back/webcontroller/pack_promo/show.html.twig',
                [
                    'products' => $entity->getProducts()->toArray()
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
        $packPromo = $this->getEntities();
        $packPromoView = $this
            ->twig
            ->render(
                'back/webcontroller/pack_promo/list.html.twig',
                [
                    'packPromo' => $packPromo
                ]
            );

        return new Response($packPromoView);
    }

    public function endPromo()
    {
        if ($this->entity instanceof PackPromo) {
            $this->entity->setEnded(true);
            $this->manager->flush();
        }
    }

    public function startPromo()
    {
        if ($this->entity instanceof PackPromo) {
            $this->entity->setStarted(true);
            $this->manager->flush();
        }
    }
}
