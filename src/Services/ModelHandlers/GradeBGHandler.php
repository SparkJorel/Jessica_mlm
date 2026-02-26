<?php

namespace App\Services\ModelHandlers;

use App\Entity\Cycle;
use App\Entity\GradeBG;
use App\Form\GradeBGType;
use App\Repository\CycleRepository;
use App\Repository\GradeBGRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeBGHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'grade_bg_list',
                'back/webcontroller/grade_bg/new.html.twig',
                'success',
                'Le bonus générationnel du grade créé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'grade_bg_list',
                'back/webcontroller/grade_bg/new.html.twig',
                'success',
                'Le bonus générationnel du grade du grade modifié avec succès'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_grade_bg_token', 'jtwc_grade_bg-delete')) {
            return $this->processRemovEntity('grade_bg_list', 'info', 'Le bonus générationnel du grade supprimé avec succès');
        } else {
            return $this->redirectAfterSubmit('grade_bg_list', 'danger', 'A problem occured when processing the request!!');
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
         * @var GradeBGRepository $repository
         */
        $repository = $this->manager->getRepository(get_class($this->entity));

        $entities = $repository->getAllActivatedBG();
        $gradeBgView = $this
                                        ->twig
                                        ->render(
                                            'back/webcontroller/grade_bg/list.html.twig',
                                            [
                                                'grade_bgs' => $entities
                                            ]
                                        );

        return new Response($gradeBgView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(GradeBGType::class, $this->entity);
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @return RedirectResponse
     */
    protected function saveEntity(string $url_name, string $type, string $message, array $params = null)
    {
        if ($this->entity->isNew()) {
            /**
             * @var CycleRepository $cycleRepository
             */
            $cycleRepository = $this->manager->getRepository(Cycle::class);
            $cycle = $cycleRepository->getLastCycle();
            $this->getEntity()->setStartedAt($cycle->getStartedAt());
            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        if ($params) {
            return $this->redirectAfterSubmit($url_name, $type, $message, $params);
        } else {
            return $this->redirectAfterSubmit($url_name, $type, $message);
        }
    }

    /**
     * Get GradeBG entity
     *
     * @return GradeBG
     */
    protected function getEntity(): GradeBG
    {
        return $this->entity;
    }

}
