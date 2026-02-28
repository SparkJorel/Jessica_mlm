<?php

namespace App\Controller\Back\WebController;

use App\Entity\Grade;
use App\Services\ModelHandlers\GradeHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeController
{
    /**
     * @var GradeHandler
     */
    private $gradeHandler;

    public function __construct(GradeHandler $gradeHandler)
    {
        $this->gradeHandler = $gradeHandler;
    }

    #[Route('/grades', name: 'grade_list', methods: ['GET'])]
    public function list()
    {
        return $this->gradeHandler->setEntity(new Grade())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grades/create', name: 'grade_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->gradeHandler->setEntity(new Grade())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grades/{id}/edit', name: 'grade_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Grade $grade)
    {
        return $this->gradeHandler->setEntity($grade)->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grades/{id}/delete', name: 'grade_delete', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Grade $grade)
    {
        return
            $this
                ->gradeHandler
                ->setEntity($grade)
                ->remove($request, $csrf);
    }
}
