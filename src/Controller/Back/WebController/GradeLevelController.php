<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeLevel;
use App\Services\ModelHandlers\GradeLevelHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeLevelController
{
    /**
     * @var GradeLevelHandler
     */
    private $gradeLevelHandler;

    public function __construct(GradeLevelHandler $gradeLevelHandler)
    {
        $this->gradeLevelHandler = $gradeLevelHandler;
    }

    #[Route('/grade-levels/list', name: 'grade_level_list', methods: ['GET'])]
    public function list()
    {
        return $this->gradeLevelHandler->setEntity(new GradeLevel())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-levels/create', name: 'grade_level_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->gradeLevelHandler->setEntity(new GradeLevel())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-levels/{id}/edit', name: 'grade_level_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, GradeLevel $gradeLevel)
    {
        return $this->gradeLevelHandler->setEntity($gradeLevel)->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-levels/{id}/delete', name: 'grade_level_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeLevel $gradeLevel)
    {
        return
            $this
                ->gradeLevelHandler
                ->setEntity($gradeLevel)
                ->remove($request, $csrf);
    }
}
