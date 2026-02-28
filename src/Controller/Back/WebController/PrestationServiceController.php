<?php

namespace App\Controller\Back\WebController;

use App\Entity\PrestationService;
use App\Entity\Service;
use App\Services\ModelHandlers\PrestationServiceHandler;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PrestationServiceController
{
    /**
     * @var PrestationServiceHandler
     */
    private $prestationServiceHandler;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        PrestationServiceHandler $prestationServiceHandler,
        EntityManagerInterface $manager
    )
    {
        $this->prestationServiceHandler = $prestationServiceHandler;
        $this->manager = $manager;
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     */
    #[Route('/services/{slug}/prestations', name: 'prestation_service_list', requirements: ['slug' => '\w+'], methods: ['GET'])]
    public function list(Service $service)
    {
        return
            $this
                ->prestationServiceHandler
                ->setService($service)
                ->setEntity((new PrestationService()))
                ->list();
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     */
    #[Route('/services/{slug}/prestations/list', name: 'prestation_service_list_for_users', requirements: ['slug' => '\w+'], methods: ['GET'])]
    public function listForUsers(Service $service)
    {
        return
            $this
                ->prestationServiceHandler
                ->setService($service)
                ->setEntity((new PrestationService()))
                ->listForUsers();
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     */
    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{slug}/prestation/new', name: 'service_prestation_new', requirements: ['slug' => '\w+'], methods: ['GET', 'POST'])]
    public function create(Request $request, Service $service)
    {
        return
            $this
                ->prestationServiceHandler
                ->setService($service)
                ->setEntity((new PrestationService()))
                ->save($request)
            ;
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     */
    #[IsGranted('ROLE_JTWC_USER')]
    #[Route('/services/prestation/{slug}/show', name: 'service_prestation_show', methods: ['GET'], requirements: ['slug' => '\w+'])]
    public function show(Service $service)
    {
        return $this
                    ->prestationServiceHandler
                    ->setEntity($service)
                    ->show();
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("prestation", options={"mapping": {"prestation_slug": "slug"}})
     */
    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{slug}/prestation/{prestation_slug}/edit', name: 'service_prestation_edit', methods: ['GET', 'POST'], requirements: ['slug' => '\w+', 'prestation_slug' => '\w+'])]
    public function edit(
        Request $request,
        Service $service,
        PrestationService $prestation
    )
    {
        return
            $this
                ->prestationServiceHandler
                ->setService($service)
                ->setEntity($prestation)
                ->save($request);
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("prestation", options={"mapping": {"prestation_slug": "slug"}})
     */
    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{slug}/prestation/{prestation_slug}/delete', name: 'service_prestation_delete', methods: ['DELETE'], requirements: ['slug' => '\w+', 'prestation_slug' => '\w+'])]
    public function remove(
        Request $request,
        CsrfTokenManagerInterface $csrf,
        Service $service,
        PrestationService $prestation
    )
    {
        return  $this
            ->prestationServiceHandler
            ->setService($service)
            ->setEntity($prestation)
            ->remove($request, $csrf);
    }

    /**
     * @ParamConverter("service", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("prestation", options={"mapping": {"prestation_slug": "slug"}})
     */
    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/services/{slug}/prestation/{prestation_slug}/delete', name: 'service_prestation_delete', methods: ['DELETE'], requirements: ['slug' => '\w+', 'prestation_slug' => '\w+'])]
    public function activatePrestationService(
        Request $request,
        CsrfTokenManagerInterface $csrf,
        Service $service,
        PrestationService $prestation
    )
    {
        return  $this
                    ->prestationServiceHandler
                    ->setService($service)
                    ->setEntity($prestation)
                    ->activate($request, $csrf);
    }
}
