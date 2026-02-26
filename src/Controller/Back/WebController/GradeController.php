<?php

namespace App\Controller\Back\WebController;

use App\Entity\Grade;
use App\Services\ModelHandlers\GradeHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/grades", name="grade_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->gradeHandler->setEntity(new Grade())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grades/create", name="grade_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return $this->gradeHandler->setEntity(new Grade())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grades/{id}/edit", name="grade_edit",
     *     requirements={"id": "\d+"}, methods={"GET", "POST"})
     * @param Request $request
     * @param Grade $grade
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, Grade $grade)
    {
        return $this->gradeHandler->setEntity($grade)->save($request);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grades/{id}/delete", name="grade_delete",
     *     methods={"GET", "POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param Grade $grade
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Grade $grade)
    {
        return
            $this
                ->gradeHandler
                ->setEntity($grade)
                ->remove($request, $csrf);
    }
}
