<?php

namespace App\Controller\Back\WebController;

use App\Entity\GradeBG;
use App\Services\ModelHandlers\GradeBGHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

    /**
     * @Route("/grade-bgs/list", name="grade_bg_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return $this->gradeBGHandler->setEntity(new GradeBG())->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-bgs/create", name="grade_bg_new",
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
        return $this->gradeBGHandler->setEntity(new GradeBG())->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-bgs/{id}/edit", name="grade_bg_edit",
     *     requirements={"id": "\d+"}, methods={"GET", "POST"})
     * @param Request $request
     * @param GradeBG $gradeBg
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, GradeBG $gradeBg)
    {
        return $this->gradeBGHandler->setEntity($gradeBg)->save($request);
    }


    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/grade-bgs/{id}/delete", name="grade_bg_delete",
     *     methods="DELETE", requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param GradeBG $gradeBg
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, GradeBG $gradeBg)
    {
        return
            $this
                ->gradeBGHandler
                ->setEntity($gradeBg)
                ->remove($request, $csrf);
    }
}
