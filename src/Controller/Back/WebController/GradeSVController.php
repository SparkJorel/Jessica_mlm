<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeSV;
use App\Services\ModelHandlers\GradeSVHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeSVController
{
    /**
     * @var GradeSVHandler
     */
    private $gradeSVHandler;

    public function __construct(GradeSVHandler $gradeSVHandler)
    {
        $this->gradeSVHandler = $gradeSVHandler;
    }

    #[Route('/grade-svs/list', name: 'grade_sv_list', methods: ['GET'])]
    public function list()
    {
        return $this->gradeSVHandler->setEntity(new GradeSV())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-svs/create', name: 'grade_sv_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->gradeSVHandler->setEntity(new GradeSV())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-svs/{id}/edit', name: 'grade_sv_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, GradeSV $gradeSv)
    {
        return $this->gradeSVHandler->setEntity($gradeSv)->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-svs/{id}/delete', name: 'grade_sv_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeSV $gradeSv)
    {
        return
            $this
                ->gradeSVHandler
                ->setEntity($gradeSv)
                ->remove($request, $csrf);
    }
}
