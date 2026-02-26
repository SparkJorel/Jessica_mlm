<?php

namespace App\Controller\Back\WebController;

use App\Entity\AnalyseFonctionnelleSystematique;
use App\Services\ModelHandlers\AnalyseFonctionnelleSystematiqueHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/analyse-fonctionnelle-systematiques", name="analyse_fonctionnelle_systematique_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return  $this->handler->setEntity(new AnalyseFonctionnelleSystematique())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/analyse-fonctionnelle-systematiques/create", name="analyse_fonctionnelle_systematique_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return  $this->handler->setEntity(new AnalyseFonctionnelleSystematique())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/analyse-fonctionnelle-systematiques/{id}/edit", name="analyse_fonctionnelle_systematique_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param AnalyseFonctionnelleSystematique $analyse
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, AnalyseFonctionnelleSystematique $analyse)
    {
        return  $this->handler->setEntity($analyse)->save($request);
    }
}
