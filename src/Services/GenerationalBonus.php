<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\FiltreCycle;
use App\Entity\Grade;
use App\Entity\GradeBG;
use App\Entity\GradeLevel;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Form\FiltreCycleType;
use App\Repository\GradeBGRepository;
use App\Repository\GradeLevelRepository;
use App\Repository\ParameterConfigRepository;
use App\Repository\UserGradeRepository;
use App\Repository\UserRepository;
use App\Repository\CycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserGrade as UserGradeModel;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GenerationalBonus
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var BonusBinary
     */
    private $bonusBinary;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        Environment $twig,
        BonusBinary $bonusBinary
    )
    {
        $this->manager = $manager;
        $this->bonusBinary = $bonusBinary;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param User|null $user
     * @param Cycle|null $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function bonusGenerational(Request $request, User $user = null, Cycle $cycle = null)
    {
        if (!$user) {
            /**
             * @var User $user
             */
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        if ($user->getMembership()->getCoefficent() < 2) {

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_generational_bonus.html.twig', [
                'form' => $form->createView(),
                'results' => null,
            ]);

        } else {
            $results = $this->computeBonusGenerationnel($user, $cycle);

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_generational_bonus.html.twig', [
                'form' => $form->createView(),
                'results' => $results,
            ]);
        }        

        return new Response($bonusView);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @param array|null $userBinary
     * @return array|null
     */
    public function computeBonusGenerationnel(User $user, Cycle $cycle, array &$userBinary = null)
    {
        $bonus_generationnels = [];
        $generationnel = 0;

        if ($user->getMembership()->getCoefficent() < 2) {
            return null;
        }

        /**
         * @var ParameterConfigRepository $repositoryCycle
         */
        $repositoryCycle = $this->manager->getRepository(ParameterConfig::class);

        $sv = $repositoryCycle->valueParameter('sv', $cycle);

        if ($user->getGrade()) {
            $grade = $user->getUserGrade();

        } else {
            $grade = $this->getUserGrade($user, $cycle);
        }

        if (!$grade) {
            return null;
        }

        $level = $grade->getLvl();

        if (!$level) {
            return null;
        }

        $bgs = $grade->getGradeBGs();

        $users_tab = $this->getUsersMatchingLevel($user, $level);

        if (!$users_tab || empty($users_tab)) {
            return null;
        }

        foreach ($users_tab as $lvl => $users) {
            $bg = $this->getBonusMatchingLevel($lvl, $bgs);

            /**
             * @var User[] $users
             */
            foreach ($users as $u) {
                if ($userBinary) {
                    $keys = array_keys($userBinary);
                    if (!in_array($u->getUsername(), $keys)) {
                        if ($cycle->getClosed() && $cycle->getBinarySaved()) {
                            $results = $this
                                        ->bonusBinary
                                        ->handleSavedCarryOver($u, $cycle);
                        } else {
                            $results = $this
                                    ->bonusBinary
                                    ->computeUserBonusGroup($u, $cycle, $sv);
                        }

                        $userBinary[$u->getUsername()] = $results;
                    } else {
                        $results = $userBinary[$u->getUsername()];
                    }
                } else {
                    if ($cycle->getClosed() && $cycle->getBinarySaved()) {
                        $results = $this->bonusBinary->handleSavedCarryOver($u, $cycle);
                    } else {
                        $results = $this->bonusBinary->computeUserBonusGroup($u, $cycle, $sv);
                    }
                }

                if (!array_key_exists('binaire', $results) || 0 === $results['binaire']) {
                    continue;
                }

                $gain = $results['gain'];
                $sv_gain = $results['sv_gain'];

                $bonus_g = [];
                $bonus_g['nom'] = $u->getFullname();
                $bonus_g['position'] = $u->getPosition();

                $bonus_g['generation'] = $lvl;
                $bonus_g['pourcentage'] = $bg;
                $bonus_g['generationnel'] = $sv_gain * $bg;
                $bonus_g['binaire'] = $sv_gain;
                $bonus_g['gain'] = $gain * $bg;
                $generationnel += $bonus_g['gain'];

                $bonus_generationnels[] = $bonus_g;
            }
        }

        $bonus_generationnels['gain'] = $generationnel;
        $bonus_generationnels['grade'] = $grade->getCommercialName();

        return $bonus_generationnels;
    }

    /**
     * @param int $lvl
     * @param Collection|GradeBG[] $bonusGenerationnel
     * @return int|float
     */
    private function getBonusMatchingLevel(int $lvl, Collection $bonusGenerationnel)
    {
        foreach ($bonusGenerationnel as $value) {
            if ($value->getLvl()->getLvl() === $lvl) {
                return (float)$value->getValue();
            }
        }
        return 0;
    }


    /**
     * @param User $user
     * @param Cycle $cycle
     * @return Grade|null
     */
    private function getUserGrade(User $user, Cycle $cycle)
    {
        /**
         * @var UserGradeRepository $repository
         */
        $repository = $this->manager->getRepository(UserGradeModel::class);

        $userGrade = $repository->getUserGradeMatchingCycle($user, $cycle);

        if (!$userGrade) {
            return null;
        }

        return $userGrade->getGrade();
    }

    /**
     * @param Grade $grade
     * @param Cycle $cycle
     * @return int|null
     */
    private function getMatchingLvl(Grade $grade, Cycle $cycle)
    {
        /**
         * @var GradeLevelRepository $repository
         */
        $repository = $this->manager->getRepository(GradeLevel::class);

        $gradeLevel =  $repository->getLeveofGradeMatchingCycle($grade, $cycle);
        if (!$gradeLevel) {
            return null;
        }

        return $gradeLevel->getLvl();
    }

    /**
     * @param User $user
     * @param int $level
     * @return array|null
     */
    private function getUsersMatchingLevel(User $user, int $level)
    {
        $users = [];

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        $users_ids = [];

        for ($i = 0; $i < $level; $i++) {
            if ($i === 0) {
                $users_ids = [$user->getId()];
            } else {
                $users_ids = array_map(function (User $user) {
                    return $user->getId();
                }, $users[$i-1]);
            }

            /** @var User[]|null $userGenerations */
            $userGenerations = $repository->getUsersMatchingLevel($users_ids);

            if (!$userGenerations) {
                break;
            }

            $users[$i+1] = $userGenerations;
        }

        unset($users_ids);

        return !empty($users) ? $users : null;
    }

    /**
     * @param Grade $grade
     * @param Cycle $cycle
     * @return array
     */
    private function getBG(Grade $grade, Cycle $cycle)
    {
        $grades = [];
        $saveInfos = [];
        /**
         * @var GradeBGRepository $repository
         */
        $repository = $this->manager->getRepository(GradeBG::class);

        $gradeBGs = $repository->getBGofGradeMatchingCycle($grade, $cycle);

        if (!$gradeBGs) {
            return $grades;
        }

        foreach ($gradeBGs as $gradeBG) {
            if (!array_key_exists($gradeBG->getName(), $saveInfos)) {
                $grades[$gradeBG->getName().'_'.$gradeBG->getLvl()->getLvl()] = $gradeBG->getValue();
                $saveInfos[$gradeBG->getName()] = $gradeBG->getStartedAt()->format('Y-m-d H:i:s');
            } else {
                if ($gradeBG->getStartedAt()->format('Y-m-d H:i:s') > $saveInfos[$gradeBG->getName()]) {
                    $grades[$gradeBG->getName().'_'.$gradeBG->getLvl()->getLvl()] = $gradeBG->getValue();
                }
            }
        }

        return $grades;
    }

    /**
     * @return FormInterface
     */
    private function createForm(): FormInterface
    {
        $filtreCycle = new FiltreCycle();
        return $this->formFactory->create(FiltreCycleType::class, $filtreCycle);
    }
}
