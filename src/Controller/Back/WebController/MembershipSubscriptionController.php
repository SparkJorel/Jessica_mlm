<?php

namespace App\Controller\Back\WebController;

use App\Entity\Membership;
use App\Entity\User;
use App\Entity\MembershipSubscription;
use App\Services\GetUpdatePrice;
use App\Services\ModelHandlers\MembershipSubscriptionHandler;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MembershipSubscriptionController
{
    /**
     * @var MembershipSubscriptionHandler
     */
    private $mbshipSubhandler;

    public function __construct(MembershipSubscriptionHandler $mbshipSubhandler)
    {
        $this->mbshipSubhandler = $mbshipSubhandler;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-subscriptions/all', name: 'membership_subscription_list', methods: ['GET'])]
    public function list(Request $request)
    {
        return
            $this
                ->mbshipSubhandler
                ->setEntity((new MembershipSubscription()))
                ->list(null, $request);
    }

    #[Route('/membership-subscriptions/personal', name: 'personal_membership_subscription', methods: ['GET'])]
    public function personalSubscription(TokenStorageInterface $token, Request $request)
    {
        if ($token->getToken()) {
            /** @var User $user */
            $user = $token->getToken()->getUser();
        } else {
            $user = null;
        }

        return
            $this
                ->mbshipSubhandler
                ->setEntity((new MembershipSubscription()))
                ->list($user, $request);
    }

    #[Route('/membership-subscriptions/{id}', name: 'membership_subscription_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(MembershipSubscription $subscription)
    {
        return $this->mbshipSubhandler->setEntity($subscription)->show();
    }


    #[Route('/membership/{code}/upgrade', name: 'membership_upgrade', methods: ['GET', 'POST'], requirements: ['code' => '[a-zA-Z]+'])]
    public function upgrade(Request $request, CsrfTokenManagerInterface $csrf, Membership $membership)
    {
        $membershipSubscription = (new MembershipSubscription())
                                        ->setMembership($membership);

        return $this->mbshipSubhandler->setEntity($membershipSubscription)->upgradePack($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-subscriptions/upgrade', name: 'membership_subscription_upgrade', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $mbship = new MembershipSubscription();

        return
            $this
                ->mbshipSubhandler
                ->setEntity($mbship)
                ->upgrade($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-subscriptions/{id}/edit', name: 'membership_subscription_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, MembershipSubscription $subscription)
    {
        return
            $this
                ->mbshipSubhandler
                ->setEntity($subscription)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-subscriptions/{id}/delete', name: 'membership_subscription_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, MembershipSubscription $subscription)
    {
        return  $this
                    ->mbshipSubhandler
                    ->setEntity($subscription)
                    ->remove($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/membership-subscriptions/{id}/activated', name: 'membership_subscription_activated', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function activateMembershipSubscription(MembershipSubscription $membershipSubscription, RouterInterface $router): Response
    {
        $this->mbshipSubhandler->activate($membershipSubscription);
        return new RedirectResponse($router->generate('membership_subscription_list'));
    }

    #[Route('/remain/pay/get', name: 'get_left_to_pay', options: ['expose' => true])]
    public function remainToPay(Request $request, GetUpdatePrice $price, EntityManagerInterface $manager)
    {
        $pack_id = $request->request->get('pack_id');
        $user = $request->request->get('user');

        /** @var Membership|null $nextMembership */
        $nextMembership = $manager->getRepository(Membership::class)->find($pack_id);

        if ($user) {
            $priceToPay = $price->getLeftToPay($nextMembership, $user);
        } else {
            $priceToPay = $price->getLeftToPay($nextMembership);
        }

        return new JsonResponse(['price' => $priceToPay]);
    }

    #[Route('/unpaid-subscription-command', name: 'unpaid_subscription_command', options: ['expose' => true])]
    public function unpaidSubscriptionCommand(EntityManagerInterface $manager, TokenStorageInterface $token): JsonResponse
    {
        if ($token->getToken()) {
            /** @var User $user */
            $user = $token->getToken()->getUser();
        } else {
            $user = null;
        }

        /** @var MembershipSubscription[]|null */
        $userSubscriptionCommands = $manager->getRepository(MembershipSubscription::class)->findBy(['createdBy' => $user, 'paid' => false]);

        if (!$userSubscriptionCommands) {
            return new JsonResponse([
                'count' => 0,
                'memberships' => []
            ]);
        }

        $mbs = [];

        foreach ($userSubscriptionCommands as $userSubscriptionCommand) {
            if ($userSubscriptionCommand->getMembership()) {
                $mbs[$userSubscriptionCommand->getMembership()->getCode()] = isset($mbs[$userSubscriptionCommand->getMembership()->getCode()]) ? $mbs[$userSubscriptionCommand->getMembership()->getCode()] + 1 : 1 ;
            }
        }

        return new JsonResponse([
            'count' => count($userSubscriptionCommands),
            'memberships' => $mbs
        ]);
    }

    #[Route('/membership-subscription/cart/summary/', name: 'summary_subscription_cart', options: ['expose' => true])]
    public function cartSubscriptionSummary(): Response
    {
        $template = 'back/webcontroller/membership_subscription/cart_subscription.html.twig';
        return $this->mbshipSubhandler->getSummaryCartSubscription($template);
    }
}
