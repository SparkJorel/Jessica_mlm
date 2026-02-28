<?php

namespace App\Controller\Back\WebController;

use App\Entity\FiltreCycle;
use App\Form\FiltreCycleType;
use App\Services\UserGrade;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

/**
 * Class UserGradeController
 * @package App\Controller\Back\WebController
 */
class UserGradeController
{
    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/grade/list', name: 'users_grade_list', methods: ['GET'])]
    public function list(Request $request, Environment $twig, FormFactoryInterface $formFactory, UserGrade $userGrade)
    {
        $cycle = null;
        $filtreCycle = new FiltreCycle();
        $form = $formFactory->create(FiltreCycleType::class, $filtreCycle);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if ($cycle) {
            $userGrades = $userGrade->listUserGrade($request, false, $cycle);
        } else {
            $userGrades = $userGrade->listUserGrade($request, false);
        }

        $userGradeView = $twig
            ->render(
                'back/webcontroller/user_grade/list.html.twig',
                [
                    'user_grades' => $userGrades,
                    'title' => 'Liste des utilisateurs ayant changé de grade'
                ]
            );

        return new Response($userGradeView);
    }

    #[Route('/personal/grade/list', name: 'personal_grade_list', methods: ['GET'])]
    public function personalGrade(Request $request, Environment $twig, UserGrade $userGrade)
    {
        $userGrades = $userGrade->listUserGrade($request, true);

        $userGradeView = $twig
            ->render(
                'back/webcontroller/user_grade/list.html.twig',
                [
                    'user_grades' => $userGrades,
                    'title' => 'Mes changements de grade'
                ]
            );

        return new Response($userGradeView);
    }
}
