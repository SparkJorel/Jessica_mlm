<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeLevel;
use App\Services\ModelHandlers\GradeLevelHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/grade-levels/list", name="grade_level_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->gradeLevelHandler->setEntity(new GradeLevel())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-levels/create", name="grade_level_new",
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
        return $this->gradeLevelHandler->setEntity(new GradeLevel())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-levels/{id}/edit", name="grade_level_edit",
     *     requirements={"id": "\d+"}, methods={"GET", "POST"})
     * @param Request $request
     * @param GradeLevel $gradeLevel
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, GradeLevel $gradeLevel)
    {
        return $this->gradeLevelHandler->setEntity($gradeLevel)->save($request);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-levels/{id}/delete", name="grade_level_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param GradeLevel $gradeLevel
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeLevel $gradeLevel)
    {
        return
            $this
                ->gradeLevelHandler
                ->setEntity($gradeLevel)
                ->remove($request, $csrf);
    }
}
