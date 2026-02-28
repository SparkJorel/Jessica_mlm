<?php

namespace App\Controller\Back\WebController;

use App\Entity\Cycle;
use App\Services\ModelHandlers\CycleHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('/cycles', name: 'cycle_list', methods: ['GET'])]
    public function list()
    {
        return  $this->cycleHandler->setEntity(new Cycle())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/cycles/create', name: 'cycle_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $cycle = (new Cycle())->setAutoSave(true);

        return  $this->cycleHandler->setEntity($cycle)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/cycles/{id}/edit', name: 'cycle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cycle $cycle)
    {
        return  $this->cycleHandler->setEntity($cycle)->save($request);
    }

    #[Route('/cycle/{id}/show', name: 'cycle_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Cycle $cycle)
    {
        return $this->cycleHandler->setEntity($cycle)->show();
    }
}
