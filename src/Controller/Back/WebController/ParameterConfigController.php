<?php

namespace App\Controller\Back\WebController;

use App\Entity\ParameterConfig;
use App\Services\ModelHandlers\ParameterConfigHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ParameterConfigController
{
    /**
     * @var ParameterConfigHandler
     */
    private $paramConfigHandler;

    public function __construct(ParameterConfigHandler $paramConfigHandler)
    {
        $this->paramConfigHandler = $paramConfigHandler;
    }

    #[Route('/param-configs', name: 'parameter_config_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->paramConfigHandler
                ->setEntity((new ParameterConfig()))
                ->list();
    }

    #[Route('/param-configs/{id}', name: 'parameter_config_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(ParameterConfig $param)
    {
        return $this->paramConfigHandler->setEntity($param)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/param-configs/new', name: 'parameter_config_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->paramConfigHandler
                ->setEntity((new ParameterConfig()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/parameter-config/{id}/delete', name: 'parameter_config_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ParameterConfig $param)
    {
        return  $this
                    ->paramConfigHandler
                    ->setEntity($param)
                    ->remove($request, $csrf);
    }
}
