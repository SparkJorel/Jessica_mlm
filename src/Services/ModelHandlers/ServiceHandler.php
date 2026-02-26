<?php

namespace App\Services\ModelHandlers;

use App\Entity\PrestationService;
use App\Entity\Service;
use App\Services\FileUploader;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use App\Form\ServiceType;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ServiceHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    use TraitHandlers;

    /**
     * @var ParameterBagInterface|null
     */
    private $parameterBag;
    /**
     * @var FileUploader|null
     */
    private $fileUploader;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session,
        FileUploader $fileUploader = null,
        ParameterBagInterface $parameterBag = null
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $session);
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
                'service_list',
                'back/webcontroller/service/new.html.twig',
                'success',
                'Service created'
            );
        } else {
            return $this->submit(
                $request,
                'service_list',
                'back/webcontroller/service/edit.html.twig',
                'success',
                'Service updated'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_service_token', 'jtwc_service-delete')) {
            return $this->processRemovEntity('service_list', 'info', 'Service deactivated');
        } else {
            return $this->redirectAfterSubmit('service_list', 'danger', 'A problem occured when processing the request!!');
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
                'back/webcontroller/service/show.html.twig',
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
        $services = $this->getEntities();
        $serviceView = $this
            ->twig
            ->render(
                'back/webcontroller/service/list.html.twig',
                [
                    'services' => $services
                ]
            );

        return new Response($serviceView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(ServiceType::class, $this->entity);
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @param FormInterface|null $form
     * @return RedirectResponse
     */
    protected function saveEntity(string $url_name, string $type, string $message, array $params = null, FormInterface $form = null)
    {
        if ($this->entity->isNew()) {
            $prestationServices = $form->get('prestationServices')->getData();

            /**
             * @var PrestationService[]|Collection $prestationServices
             */
            if (!$prestationServices->isEmpty()) {
                foreach ($prestationServices as $prestationService) {
                    $newFilename = $this->getFileName($prestationService);
                    if ($newFilename) {
                        $prestationService->setBrochureServiceFilename($newFilename);
                    }
                }
            }

            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    /**
     * @param Request $request
     * @param string $url_name
     * @param string $template
     * @param string $type
     * @param string $message
     * @param bool|null $mode
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
            return $this->saveEntity($url_name, $type, $message, null, $form);
        } else {
            return $this->renderFormView($template, $form);
        }
    }

    /**
     * @return Service
     */
    private function getService()
    {
        /**
         * @var Service $service
         */
        $service = &$this->entity;
        return $service;
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $message
     *
     * @return RedirectResponse
     */
    protected function disabledService(string $url, string $type, string $message): RedirectResponse
    {
        $this->getService()->setStatus(false);

        $this->manager->flush();

        return $this->redirectAfterSubmit($url, $type, $message);
    }

    /**
     * @return RedirectResponse
     */
    public function disabled()
    {
        return $this->disabledService('service_list', 'info', 'Service deactivated');
    }
}
