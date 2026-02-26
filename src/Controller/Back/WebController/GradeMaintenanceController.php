<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeMaintenance;
use App\Services\ModelHandlers\GradeMaintenanceHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/grade-maintenances/list", name="grade_maintenance_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->gradeMaintenanceHandler->setEntity(new GradeMaintenance())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-maintenances/create", name="grade_maintenance_new",
     *     methods={"GET", "POST"}
     * )
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return $this->gradeMaintenanceHandler->setEntity(new GradeMaintenance())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-maintenances/{id}/edit", name="grade_maintenance_edit",
     *     requirements={"id": "\d+"}, methods={"GET", "POST"})
     * @param Request $request
     * @param GradeMaintenance $gradeMaintenance
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, GradeMaintenance $gradeMaintenance)
    {
        return $this->gradeMaintenanceHandler->setEntity($gradeMaintenance)->save($request);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-maintenances/{id}/delete", name="grade_maintenance_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param GradeMaintenance $gradeMaintenance
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeMaintenance $gradeMaintenance)
    {
        return
            $this
                ->gradeMaintenanceHandler
                ->setEntity($gradeMaintenance)
                ->remove($request, $csrf);
    }
}
