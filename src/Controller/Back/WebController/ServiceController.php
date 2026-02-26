<?php

namespace App\Controller\Back\WebController;

use App\Entity\Service;
use App\Services\ModelHandlers\ServiceHandler;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ServiceController
{
    /**
     * @var ServiceHandler
     */
    private $serviceHandler;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ServiceHandler $serviceHandler, EntityManagerInterface $manager)
    {
        $this->serviceHandler = $serviceHandler;
        $this->manager = $manager;
    }

    /**
     * @Route("/services", name="service_list", methods={"GET"})
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        return
            $this
                ->serviceHandler
                ->setEntity((new Service()))
                ->list();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/service/new", name="service_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        return
            $this
                ->serviceHandler
                ->setEntity((new Service()))
                ->save($request)
            ;
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/services/{id}", name="service_show", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param Service $service
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Service $service)
    {
        return $this->serviceHandler->setEntity($service)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/services/{id}/edit", name="service_edit",
     *     methods={"GET","POST"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param Service $service
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, Service $service)
    {
        return
            $this
                ->serviceHandler
                ->setEntity($service)
                ->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/services/{id}/delete", name="service_delete",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param Service $service
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Service $service)
    {
        return  $this
            ->serviceHandler
            ->setEntity($service)
            ->remove($request, $csrf);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/services/{id}/disabled", name="service_disabled",
     *     methods={"GET"}, requirements={"id": "\d+"}
     * )
     * @param Service $service
     * @return RedirectResponse
     */
    public function disableService(Service $service)
    {
        return  $this
            ->serviceHandler
            ->setEntity($service)
            ->disabled();
    }
}
