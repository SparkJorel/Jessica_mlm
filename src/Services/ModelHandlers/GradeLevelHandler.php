<?php

namespace App\Services\ModelHandlers;

use App\Entity\GradeBG;
use App\Entity\GradeLevel;
use App\Form\GradeLevelType;
use App\Repository\GradeLevelRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeLevelHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'grade_level_list',
                'back/webcontroller/grade_level/new.html.twig',
                'success',
                'Nombre de génération du grade créé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'grade_level_list',
                'back/webcontroller/grade_level/new.html.twig',
                'success',
                'Nombre de génération du grade modifié avec succès'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_grade_level_token', 'jtwc_grade_level-delete')) {
            return $this->processRemovEntity('grade_level_list', 'info', 'Nombre de génération du grade supprimé');
        } else {
            return $this->redirectAfterSubmit('grade_level_list', 'danger', 'A problem occured when processing the request!!');
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
         * @var GradeLevelRepository $repository
         */
        $repository = $this->manager->getRepository(get_class($this->entity));

        $entities = $repository->getAllActivatedLevel();
        $gradeLevelView = $this
                                            ->twig
                                            ->render(
                                                'back/webcontroller/grade_level/list.html.twig',
                                                [
                                                    'grade_levels' => $entities
                                                ]
                                            );

        return new Response($gradeLevelView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(GradeLevelType::class, $this->entity);
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @param FormInterface|null $form
     * @return RedirectResponse|void
     */
    protected function saveEntity(string $url_name, string $type, string $message, array $params = null, FormInterface $form = null)
    {
        $grade = $this->getGradeLevel()->getGrade();

        if ($this->entity->isNew()) {
            $bgs = $form->get('bgs')->getData();

            foreach ($bgs as $bg) {
                $gradeBG = (new GradeBG())
                    ->setGrade($grade)
                    ->setStatus(false)
                    ->setName($bg['name'])
                    ->setValue($bg['value']);

                $this->manager->persist($gradeBG);
            }

            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    /**
     * @return GradeLevel
     */
    private function getGradeLevel()
    {
        /**
         * @var GradeLevel $gradeLevel
         */
        $gradeLevel = &$this->entity;
        return $gradeLevel;
    }
}
