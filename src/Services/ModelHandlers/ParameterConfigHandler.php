<?php

namespace App\Services\ModelHandlers;

use App\Entity\ParameterConfig;
use App\Form\ParameterConfigType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ParameterConfigHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this
                    ->formFactory
                    ->create(
                        ParameterConfigType::class,
                        $this->entity
                    );
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
        // TODO: Implement save() method.
        return $this
                    ->submit(
                        $request,
                        'parameter_config_list',
                        'back/webcontroller/parameter_config/new.html.twig',
                        'success',
                        'Parameter created'
                    );
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, '_jtwc_parameter_config_token', 'jtwc_parameter_config-delete')) {
            return $this
                ->processRemovEntity(
                    'parameter_config_list',
                    'info',
                    'Parameter deactivated'
                );
        } else {
            return $this
                ->redirectAfterSubmit(
                    'parameter_config_list',
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
        // TODO: Implement show() method.
        return $this->getEntityView('back/webcontroller/parameter_config/show.html.twig');
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        // TODO: Implement list() method.
        $params = $this->getEntities();
        $paramsView = $this
                            ->twig
                            ->render(
                                'back/webcontroller/parameter_config/list.html.twig',
                                [
                                    'params' => $params
                                ]
                            );

        return new Response($paramsView);
    }

    protected function processRemovEntity(string $url, string $type, $message, ?bool $mode = false, array $params = null)
    {
        if (!($this->entity instanceof ParameterConfig)) {
            return null;
        }

        $this->entity->setRemoved(true);
        $this->manager->flush();
        return $this->redirectAfterSubmit($url, $type, $message);
    }
}
