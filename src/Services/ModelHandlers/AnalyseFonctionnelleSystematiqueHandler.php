<?php

namespace App\Services\ModelHandlers;

use App\Form\AnalyseFonctionnelleSystematiqueType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AnalyseFonctionnelleSystematiqueHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'analyse_fonctionnelle_systematique_list',
                'back/webcontroller/analyse_fonctionnelle_systematique/new.html.twig',
                'success',
                'Élement créé'
            );
        } else {
            return $this->submit(
                $request,
                'analyse_fonctionnelle_systematique_list',
                'back/webcontroller/analyse_fonctionnelle_systematique/new.html.twig',
                'success',
                'Élement mis à jour'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_analyse_fonctionnelle_systematique_token', 'jtwc_analyse_fonctionnelle_systematique-delete')) {
            return $this->processRemovEntity('analyse_fonctionnelle_systematique_list', 'info', 'Élément de l\'analyse fonctionnelle systématique créé');
        } else {
            return $this->redirectAfterSubmit('analyse_fonctionnelle_systematique_list', 'danger', 'A problem occured when processing the request!!');
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
            ->getEntityView('back/webcontroller/analyse_fonctionnelle_systematique/show.html.twig');
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        $analyseFonctionnelleSystematiques = $this->getEntities();
        $analyseFonctionnelleSystematiquesView = $this
            ->twig
            ->render(
                'back/webcontroller/analyse_fonctionnelle_systematique/list.html.twig',
                [
                    'analyseFonctionnelleSystematiques' => $analyseFonctionnelleSystematiques
                ]
            );

        return new Response($analyseFonctionnelleSystematiques);
    }

    protected function createForm(): FormInterface
    {
        return $this
                    ->formFactory
                    ->create(
                        AnalyseFonctionnelleSystematiqueType::class,
                        $this->entity
                    );
    }
}
