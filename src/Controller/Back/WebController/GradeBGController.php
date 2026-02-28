<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeBG;
use App\Services\ModelHandlers\GradeBGHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeBGController
{
    /**
     * @var GradeBGHandler
     */
    private $gradeBGHandler;

    public function __construct(GradeBGHandler $gradeBGHandler)
    {
        $this->gradeBGHandler = $gradeBGHandler;
    }

    #[Route('/grade-bgs/list', name: 'grade_bg_list', methods: ['GET'])]
    public function list()
    {
        return $this->gradeBGHandler->setEntity(new GradeBG())->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-bgs/create', name: 'grade_bg_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->gradeBGHandler->setEntity(new GradeBG())->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-bgs/{id}/edit', name: 'grade_bg_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, GradeBG $gradeBg)
    {
        return $this->gradeBGHandler->setEntity($gradeBg)->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/grade-bgs/{id}/delete', name: 'grade_bg_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeBG $gradeBg)
    {
        return
            $this
                ->gradeBGHandler
                ->setEntity($gradeBg)
                ->remove($request, $csrf);
    }
}
