<?php

namespace App\Services\ModelHandlers;

use App\AbstractModel\EntityInterface;
use App\Entity\PrestationService;
use App\Entity\Service;
use App\Event\PrestationServiceActivatedEvent;
use App\Form\PrestationServiceType;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PrestationServiceHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    use TraitHandlers;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session,
        EventDispatcherInterface $dispatcher,
        ParameterBagInterface $parameterBag,
        FileUploader $fileUploader
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $session);
        $this->dispatcher = $dispatcher;
        $this->parameterBag = $parameterBag;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false)
    {
        if ($this->entity->isNew()) {
            return $this->submit(
                $request,
                'prestation_service_list',
                'back/webcontroller/prestation_service/new.html.twig',
                'success',
                'Service delivery created'
            );
        } else {
            return $this->submit(
                $request,
                'prestation_service_list',
                'back/webcontroller/prestation_service/edit.html.twig',
                'success',
                'Service delivery updated'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid(
            $csrf,
            $request,
            '_jtwc_prestation_service_token',
            'jtwc_prestation_service-delete'
        )) {
            return $this->processRemovEntity(
                'prestation_service_list',
                'info',
                'Service delivery deactivated'
            );
        } else {
            return $this->redirectAfterSubmit(
                'prestation_service_list',
                'danger',
                'A problem occured when processing the request!!'
            );
        }
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        return new Response(
            $this->twig->render(
                'back/webcontroller/prestation_service/show.html.twig',
                [
                    'entity' => $this->entity
                ]
            )
        );
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        $template = 'back/webcontroller/prestation_service/list.html.twig';

        return $this->getListServices($template);
    }


    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function listForUsers()
    {
        $template = 'back/webcontroller/prestation_service/list_for_users.html.twig';

        return $this->getListServices($template);
    }

    /**
     * @param string $template
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function getListServices(string $template)
    {
        $prestationServices = $this->getEntities();
        $prestationServicesView = $this
            ->twig
            ->render(
                $template,
                [
                    'prestationServices' => $prestationServices
                ]
            );
        return new Response($prestationServicesView);
    }

    protected function createForm(): FormInterface
    {
        return $this
                    ->formFactory
                    ->create(
                        PrestationServiceType::class,
                        $this->entity
                    );
    }

    public function setService(Service $service)
    {
        $this->getPrestationService()->setService($service);
        return $this;
    }

    /**
     * @return PrestationService
     */
    public function getPrestationService()
    {
        /**
         * @var PrestationService $entity
         */
        $entity = &$this->entity;

        return $entity;
    }

    public function getEntities()
    {
        /**
         * @var EntityInterface[] $entities
         */
        $entities = $this
            ->manager
            ->getRepository(get_class($this->entity))
            ->findBy([
                'service' => $this->getPrestationService()->getService()
            ]);

        return $entities;
    }

    /**
     * @param Request $request
     * @param string $url_name
     * @param string $template
     * @param string $type
     * @param string $message
     * @param bool|null $mode
     * @param array|null $params
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function submit(Request $request, string $url_name, string $template, string $type, string $message, ?bool $mode = false, array $params = null): Response
    {
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($this->validate($form)) {
            return $this->saveEntity($url_name, $type, $message);
        } else {
            return $this->renderFormView($template, $form);
        }
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @return RedirectResponse
     * @throws Exception
     */
    public function activate(
        Request $request,
        CsrfTokenManagerInterface $csrf
    )
    {
        if ($this->isTokenValid(
            $csrf,
            $request,
            '_jtwc_prestation_service_activated_token',
            'jtwc_prestation_service_activated-delete'
        )) {
            $this->getPrestationService()->setStatus(true);

            $this->manager->flush();

            return $this->redirectAfterSubmit(
                'prestation_service_list',
                'info',
                'Service provision activated'
            );
        } else {
            return $this
                        ->redirectAfterSubmit(
                            'prestation_service_list',
                            'danger',
                            'Activation failed!!'
                        );
        }
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @return RedirectResponse
     */
    protected function saveEntity(
        string $url_name,
        string $type,
        string $message,
        array $params = null
    )
    {
        $prestationService = $this->getPrestationService();

        $newFilename = $this->getFileName($prestationService);

        if ($newFilename) {
            $prestationService->setBrochureServiceFilename($newFilename);
        }

        return parent::saveEntity($url_name, $type, $message);
    }
}
