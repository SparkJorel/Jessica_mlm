<?php

namespace App\Controller\Back\WebController;

use App\Entity\Cycle;
use App\Entity\MembershipSubscription;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use App\Entity\UserCommands;
use App\Repository\CycleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class DashboardController
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;
    private Environment $twig;

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        Environment $twig
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
    }

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $roles = $user->getRoles();
        $isAdmin = in_array('ROLE_JTWC_ADMIN', $roles) || in_array('ROLE_JTWC_USER_SECRET', $roles);

        /** @var CycleRepository $cycleRepo */
        $cycleRepo = $this->em->getRepository(Cycle::class);
        $currentCycle = $cycleRepo->getLastCycle();

        if ($isAdmin) {
            $data = $this->getAdminDashboardData($user, $currentCycle);
            $template = 'back/webcontroller/dashboard/admin.html.twig';
        } else {
            $data = $this->getUserDashboardData($user, $currentCycle);
            $template = 'back/webcontroller/dashboard/user.html.twig';
        }

        $data['user'] = $user;
        $data['currentCycle'] = $currentCycle;

        return new Response($this->twig->render($template, $data));
    }

    private function getAdminDashboardData(User $user, ?Cycle $cycle): array
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->em->getRepository(User::class);

        // Total members
        $totalMembers = (int) $this->em->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u'
        )->getSingleScalarResult();

        // Active members
        $activeMembers = (int) $this->em->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.activated = true'
        )->getSingleScalarResult();

        // Inactive members
        $inactiveMembers = $totalMembers - $activeMembers;

        // Total subscriptions revenue
        $totalRevenue = (float) $this->em->createQuery(
            'SELECT COALESCE(SUM(ms.price), 0) FROM App\Entity\MembershipSubscription ms WHERE ms.paid = true'
        )->getSingleScalarResult();

        // Pending subscriptions (not paid)
        $pendingSubscriptions = (int) $this->em->createQuery(
            'SELECT COUNT(ms.id) FROM App\Entity\MembershipSubscription ms WHERE ms.paid = false'
        )->getSingleScalarResult();

        // Total orders paid
        $totalOrders = (int) $this->em->createQuery(
            'SELECT COUNT(uc.id) FROM App\Entity\UserCommands uc WHERE uc.paid = true AND uc.status = :status'
        )->setParameter('status', UserCommands::STATUS_ORDERED)->getSingleScalarResult();

        // Total sponsoring bonuses
        $totalSponsoringBonus = (float) $this->em->createQuery(
            'SELECT COALESCE(SUM(sb.value), 0) FROM App\Entity\SponsoringBonus sb'
        )->getSingleScalarResult();

        // Recent activations (last 10)
        $recentActivations = $this->em->createQuery(
            'SELECT u FROM App\Entity\User u WHERE u.activated = true ORDER BY u.dateActivation DESC'
        )->setMaxResults(10)->getResult();

        // Recent orders (last 10)
        $recentOrders = $this->em->createQuery(
            'SELECT uc FROM App\Entity\UserCommands uc WHERE uc.status = :status ORDER BY uc.dateCommand DESC'
        )->setParameter('status', UserCommands::STATUS_ORDERED)->setMaxResults(10)->getResult();

        return [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'inactiveMembers' => $inactiveMembers,
            'totalRevenue' => $totalRevenue,
            'pendingSubscriptions' => $pendingSubscriptions,
            'totalOrders' => $totalOrders,
            'totalSponsoringBonus' => $totalSponsoringBonus,
            'recentActivations' => $recentActivations,
            'recentOrders' => $recentOrders,
        ];
    }

    private function getUserDashboardData(User $user, ?Cycle $cycle): array
    {
        // Direct downline count
        $directDownline = (int) $this->em->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.sponsor = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        // Total network (nested set: all nodes between lft and rgt)
        $networkSize = 0;
        if ($user->getLft() && $user->getRgt()) {
            $networkSize = (int)(($user->getRgt() - $user->getLft() - 1) / 2);
        }

        // My sponsoring bonuses total
        $mySponsoringBonus = (float) $this->em->createQuery(
            'SELECT COALESCE(SUM(sb.value), 0) FROM App\Entity\SponsoringBonus sb WHERE sb.sponsor = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        // My orders count
        $myOrdersCount = (int) $this->em->createQuery(
            'SELECT COUNT(uc.id) FROM App\Entity\UserCommands uc WHERE uc.user = :user AND uc.status = :status'
        )->setParameter('user', $user)->setParameter('status', UserCommands::STATUS_ORDERED)->getSingleScalarResult();

        // Membership info
        $membership = $user->getMembership();

        // Active subscription
        $activeSubscription = $this->em->getRepository(MembershipSubscription::class)
            ->findOneBy(['member' => $user, 'paid' => true], ['id' => 'DESC']);

        // Recent sponsoring bonuses
        $recentBonuses = $this->em->createQuery(
            'SELECT sb FROM App\Entity\SponsoringBonus sb WHERE sb.sponsor = :user ORDER BY sb.dateActivation DESC'
        )->setParameter('user', $user)->setMaxResults(5)->getResult();

        return [
            'directDownline' => $directDownline,
            'networkSize' => $networkSize,
            'mySponsoringBonus' => $mySponsoringBonus,
            'myOrdersCount' => $myOrdersCount,
            'membership' => $membership,
            'activeSubscription' => $activeSubscription,
            'recentBonuses' => $recentBonuses,
        ];
    }
}
