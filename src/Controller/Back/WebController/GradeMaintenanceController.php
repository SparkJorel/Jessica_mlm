<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeMaintenance;
use App\Services\ModelHandlers\GradeMaintenanceHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeMaintenanceController
{
    /**
     * @var GradeMaintenanceHandler
     */
    private $gradeMaintenanceHandler;

    public function __construct(GradeMaintenanceHandler $gradeMaintenanceHandler)
    {
        $this->gradeMaintenanceHandler = $gradeMaintenanceHandler;
    }

    #[Route('/grade-maintenances/list', name: 'grade_maintenance_list', methods: ['GET'])]
    public function list()
    {
        return $this->gradeMaintenanceHandler->setEntity(new GradeMaintenance())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-maintenances/create', name: 'grade_maintenance_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->gradeMaintenanceHandler->setEntity(new GradeMaintenance())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-maintenances/{id}/edit', name: 'grade_maintenance_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, GradeMaintenance $gradeMaintenance)
    {
        return $this->gradeMaintenanceHandler->setEntity($gradeMaintenance)->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-maintenances/{id}/delete', name: 'grade_maintenance_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeMaintenance $gradeMaintenance)
    {
        return
            $this
                ->gradeMaintenanceHandler
                ->setEntity($gradeMaintenance)
                ->remove($request, $csrf);
    }
}
