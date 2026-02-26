<?php

namespace App\Services\ModelHandlers;

use App\Entity\Grade;
use App\Entity\GradeBG;
use App\Form\GradeType;
use App\Repository\GradeRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GradeHandler extends ModelSingleEntityAbstract implements ModelInterface
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
                'grade_list',
                'back/webcontroller/grade/new.html.twig',
                'success',
                'Grade créé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'grade_list',
                'back/webcontroller/grade/edit.html.twig',
                'success',
                'Grade modifié avec succès'
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_grade_token', 'jtwc_grade-delete')) {
            return $this->processRemovEntity('grade_list', 'info', 'Grade supprimé');
        } else {
            return $this->redirectAfterSubmit('grade_list', 'danger', 'A problem occured when processing the request!!');
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
        $grades = $this->getEntities();
        $gradeView = $this
                                    ->twig
                                    ->render(
                                        'back/webcontroller/grade/list.html.twig',
                                        [
                                            'grades' => $grades
                                        ]
                                    );

        return new Response($gradeView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(GradeType::class, $this->entity);
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
        if ($this->entity->isNew()) {
            $grade = $this->getGrade();

            $bgs = $form->get('bgs')->getData();

            foreach ($bgs as $bg) {
                $gradeBG = (new GradeBG())
                                    ->setGrade($grade)
                                    ->setStatus(false)
                                    ->setName($bg['name'])
                                    ->setLvl($bg['level'])
                                    ->setValue($bg['value']);

                $this->manager->persist($gradeBG);
            }

            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    /**
     * @return Grade
     */
    private function getGrade()
    {
        /**
         * @var Grade $grade
         */
        $grade = &$this->entity;
        return $grade;
    }

    protected function getEntities()
    {
        $results = [];
        /**
         * @var GradeRepository $repository
         */
        $repository = $this
                            ->manager
                            ->getRepository(get_class($this->entity));

        /** @var Grade[]|null $grades */
        $grades = $repository->getAll();

        foreach ($grades as $grade) {
            if ($grade->getGradeBGs()->isEmpty()) {
                continue;
            }
            $result['id'] = $grade->getId();
            $result['commercialName'] = $grade->getCommercialName();
            $result['maintenance'] = $grade->getMaintenance();
            $result['sv'] = $grade->getSV();
            $result['lvl'] = $grade->getLvl();
            $result['rewardable'] = $grade->isRewardable();
            $result['generational'] = $this->getBGB($grade->getGradeBGs());

            array_push($results, $result);
        }

        return $results;
    }

    /**
     * @param GradeBG[]|Collection $gradesBGs
     * @return array
     */
    private function getBGB($gradesBGs)
    {
        $bgbs = [];

        foreach ($gradesBGs as $gradesBG) {
            $bgb['level'] = $gradesBG->getLvl()->getLvl();
            $bgb['name'] = $gradesBG->getName();
            $bgb['value'] = $gradesBG->getValue();

            array_push($bgbs, $bgb);
        }
        return $bgbs;
    }
}
