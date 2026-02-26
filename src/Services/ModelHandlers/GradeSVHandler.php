<?php

namespace App\Services\ModelHandlers;

use App\Form\GradeSVType;
use App\Repository\GradeSVRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeSVHandler extends ModelSingleEntityAbstract implements ModelInterface
{
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
                'grade_sv_list',
                'back/webcontroller/grade_sv/new.html.twig',
                'success',
                'Le nombre de SV du grade créé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'grade_sv_list',
                'back/webcontroller/grade_sv/new.html.twig',
                'success',
                'Le nombre de SV du grade du grade modifié avec succès'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_grade_sv_token', 'jtwc_grade_sv-delete')) {
            return $this->processRemovEntity('grade_sv_list', 'info', 'Le nombre SV du grade supprimé avec succès');
        } else {
            return $this->redirectAfterSubmit('grade_sv_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    public function show()
    {
        // TODO: Implement show() method.
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list()
    {
        /**
         * @var GradeSVRepository $repository
         */
        $repository = $this->manager->getRepository(get_class($this->entity));

        $entities = $repository->getAllActivatedSV();
        $gradeSvView = $this
            ->twig
            ->render(
                'back/webcontroller/grade_sv/list.html.twig',
                [
                    'grade_svs' => $entities
                ]
            );

        return new Response($gradeSvView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(GradeSVType::class, $this->entity);
    }
}
