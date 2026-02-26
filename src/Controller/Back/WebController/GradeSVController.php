<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeSV;
use App\Services\ModelHandlers\GradeSVHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/grade-svs/list", name="grade_sv_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->gradeSVHandler->setEntity(new GradeSV())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-svs/create", name="grade_sv_new",
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
        return $this->gradeSVHandler->setEntity(new GradeSV())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-svs/{id}/edit", name="grade_sv_edit",
     *     requirements={"id": "\d+"}, methods={"GET", "POST"})
     * @param Request $request
     * @param GradeSV $gradeSv
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, GradeSV $gradeSv)
    {
        return $this->gradeSVHandler->setEntity($gradeSv)->save($request);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-svs/{id}/delete", name="grade_sv_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param GradeSV $gradeSv
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeSV $gradeSv)
    {
        return
            $this
                ->gradeSVHandler
                ->setEntity($gradeSv)
                ->remove($request, $csrf);
    }
}
