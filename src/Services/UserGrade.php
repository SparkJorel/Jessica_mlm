<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\Grade;
use App\Entity\User;
use App\Entity\UserGrade as UserGradeModel;
use App\Repository\UserGradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserGrade
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ComputeDateOperation
     */
    private $dateOperation;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var TokenStorageInterface
     */
    private $token;

    public function __construct(
        EntityManagerInterface $manager,
        EventDispatcherInterface $dispatcher,
        PaginatorInterface $paginator,
        TokenStorageInterface $token,
        ComputeDateOperation $dateOperation
    )
    {
        $this->manager = $manager;
        $this->dateOperation = $dateOperation;
        $this->dispatcher = $dispatcher;
        $this->paginator = $paginator;
        $this->token = $token;
    }

    public function listUserGrade(Request $request, bool $own, Cycle $cycle = null)
    {
        if ($own) {
            $userGradeQuery = $this->getPersonalGrade();
        } else {
            $userGradeQuery = $this->getUsersGrade($cycle);
        }
        return $this
                        ->paginator->paginate(
                            $userGradeQuery,
                            $request->query->get('page', 1),
                            10
                        );
    }

    private function getUsersGrade(Cycle $cycle = null)
    {
        /**
         * @var UserGradeRepository $repository
         */
        $repository = $this->manager->getRepository(UserGradeModel::class);

        if ($cycle) {
            $userGradeQuery = $repository->getUserGradeChange($cycle);
        } else {
            $userGradeQuery = $repository->getUserGradeChange();
        }

        return $userGradeQuery;
    }

    private function getPersonalGrade()
    {
        /**
         * @var User $user
         */
        $user = $this->token->getToken()->getUser();
        /**
         * @var UserGradeRepository $repository
         */
        $repository = $this->manager->getRepository(UserGradeModel::class);

        return $repository->getAllUserGrade($user);
    }
}
