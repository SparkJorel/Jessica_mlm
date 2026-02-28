<?php

namespace App\Controller\Back\WebController;

use App\Entity\Service;
use App\Services\ModelHandlers\ServiceHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('/services', name: 'service_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->serviceHandler
                ->setEntity((new Service()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/service/new', name: 'service_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->serviceHandler
                ->setEntity((new Service()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{id}', name: 'service_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Service $service)
    {
        return $this->serviceHandler->setEntity($service)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{id}/edit', name: 'service_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Service $service)
    {
        return
            $this
                ->serviceHandler
                ->setEntity($service)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{id}/delete', name: 'service_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, Service $service)
    {
        return  $this
            ->serviceHandler
            ->setEntity($service)
            ->remove($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{id}/disabled', name: 'service_disabled', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function disableService(Service $service)
    {
        return  $this
            ->serviceHandler
            ->setEntity($service)
            ->disabled();
    }
}
