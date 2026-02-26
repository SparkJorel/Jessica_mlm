<?php

namespace App\Services\ModelHandlers;

use App\Form\GradeMaintenanceType;
use App\Repository\GradeMaintenanceRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeMaintenanceHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'grade_maintenance_list',
                'back/webcontroller/grade_maintenance/new.html.twig',
                'success',
                'La maintenance du grade créée avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'grade_maintenance_list',
                'back/webcontroller/grade_maintenance/new.html.twig',
                'success',
                'La maintenance du grade du grade modifiée avec succès'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_grade_maintenance_token', 'jtwc_grade_maintenance-delete')) {
            return $this->processRemovEntity('grade_maintenance_list', 'info', 'La maintenance du grade supprimée avec succès');
        } else {
            return $this->redirectAfterSubmit('grade_maintenance_list', 'danger', 'A problem occured when processing the request!!');
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
        /**
         * @var GradeMaintenanceRepository $repository
         */
        $repository = $this->manager->getRepository(get_class($this->entity));

        $entities = $repository->getAllActivatedMaintenance();
        $gradeMaintView = $this
                                            ->twig
                                            ->render(
                                                'back/webcontroller/grade_maintenance/list.html.twig',
                                                [
                                                    'grade_maints' => $entities
                                                ]
                                            );

        return new Response($gradeMaintView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(GradeMaintenanceType::class, $this->entity);
    }
}
