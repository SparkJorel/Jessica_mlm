<?php

namespace App\Controller\Back\WebController;

use App\Entity\Cycle;
use App\Services\ModelHandlers\CycleHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CycleController
{
    /**
     * @var CycleHandler
     */
    private $cycleHandler;

    public function __construct(CycleHandler $cycleHandler)
    {
        $this->cycleHandler = $cycleHandler;
    }

    /**
     * @Route("/cycles", name="cycle_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return  $this->cycleHandler->setEntity(new Cycle())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/cycles/create", name="cycle_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        $cycle = (new Cycle())->setAutoSave(true);

        return  $this->cycleHandler->setEntity($cycle)->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/cycles/{id}/edit", name="cycle_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Cycle $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, Cycle $cycle)
    {
        return  $this->cycleHandler->setEntity($cycle)->save($request);
    }

    /**
     * @Route("/cycle/{id}/show", name="cycle_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param Cycle $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Cycle $cycle)
    {
        return $this->cycleHandler->setEntity($cycle)->show();
    }
}
