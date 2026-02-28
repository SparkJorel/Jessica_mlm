<?php

namespace App\Controller\Back\WebController;

use App\Entity\AnalyseFonctionnelleSystematique;
use App\Services\ModelHandlers\AnalyseFonctionnelleSystematiqueHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AnalyseFonctionnelleSystematiqueController
{
    /**
     * @var AnalyseFonctionnelleSystematiqueHandler
     */
    private $handler;

    public function __construct(AnalyseFonctionnelleSystematiqueHandler $handler)
    {
        $this->handler = $handler;
    }

    #[Route('/analyse-fonctionnelle-systematiques', name: 'analyse_fonctionnelle_systematique_list', methods: ['GET'])]
    public function list()
    {
        return  $this->handler->setEntity(new AnalyseFonctionnelleSystematique())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/analyse-fonctionnelle-systematiques/create', name: 'analyse_fonctionnelle_systematique_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return  $this->handler->setEntity(new AnalyseFonctionnelleSystematique())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/analyse-fonctionnelle-systematiques/{id}/edit', name: 'analyse_fonctionnelle_systematique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnalyseFonctionnelleSystematique $analyse)
    {
        return  $this->handler->setEntity($analyse)->save($request);
    }
}
