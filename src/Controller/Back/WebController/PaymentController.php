<?php

namespace App\Controller\Back\WebController;

use App\Entity\AddressUser;
use App\Entity\MembershipSubscription;
use App\Entity\PurchaseSummary;
use App\Entity\SendSMSPayment;
use App\Entity\UserCommands;
use App\Entity\User;
use App\Event\MembershipSubscriptionActivatedEvent;
use App\Event\ReferralBonusEvent;
use App\Form\PurchaseSummaryType;
use App\Form\SendSMSPaymentType;
use App\Repository\MembershipSubscriptionRepository;
use App\Repository\UserCommandsRepository;
use App\Services\Payment\PayInInterface;
use App\Services\ComputeDateOperation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\Payment\PayInContext;
use App\Services\GenerateUserDistributorCode;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentController extends AbstractController
{
    /** @var PayInContext */
    private $payInContext;
    
    /** @var string */
    private $apiKeyDohone;

    /** @var string */
    private $hashCode;
  
    /** @var UserCommandsRepository */
    private $userCommandsRepository;

    /** @var EntityManagerInterface */
    private $manager;

    /** @var RequestStack */
    private $requestStack;

    /** @var RouterInterface */
    private $router;

    /** @var TokenStorageInterface */
    private $token;

    /** @var MembershipSubscriptionRepository */
    private $repositorySubscription;

    /** @var ComputeDateOperation */
    private $computeDateOperation;

    /** @var GenerateUserDistributorCode */
    private $codeDistributor;

    /** @var EventDispatcherInterface */
    private $dispatcher;
  	
  	/** @var ParameterBagInterface */
  	private $parameterBag;

    public function __construct(
        PayInContext $payInContext,
        string $apiKeyDohone, string $hashCode,
        UserCommandsRepository $userCommandsRepository,
        MembershipSubscriptionRepository $repositorySubscription,
        RequestStack $requestStack,
        EntityManagerInterface $manager,
        EventDispatcherInterface $dispatcher,
        ComputeDateOperation $computeDateOperation,
        GenerateUserDistributorCode $codeDistributor,
        RouterInterface $router,
        TokenStorageInterface $token,
	  	ParameterBagInterface $parameter
    )
    {
        $this->payInContext = $payInContext;
        $this->apiKeyDohone = $apiKeyDohone;
	  	$this->hashCode = $hashCode;
        $this->userCommandsRepository = $userCommandsRepository;
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->token = $token;
        $this->repositorySubscription = $repositorySubscription;
        $this->dispatcher = $dispatcher;
        $this->computeDateOperation = $computeDateOperation;
        $this->codeDistributor = $codeDistributor;
	  	$this->parameterBag = $parameter;
    }

    #[Route('/{provider}/{operateur}/{id}/confirm/order/payment', name: 'confirm_order_payment', methods: ['POST', 'GET'], requirements: ['provider' => '\w+', 'id' => '\d+', 'operateur' => '\d+'])]
    public function payOrder(string $provider, int $operateur, UserCommands $userCommands, Request $request)
    {
        $montant = $userCommands->isDistributor() ? $userCommands->getTotalDistributorPrice() : $userCommands->getTotalClientPrice();

        $purchaseSummary = new PurchaseSummary(
            $montant,
            $userCommands->getMotif(),
            $provider,
            (int)$operateur,
            'payment_product_success',
            'command_payment_notify',
            'payment_product_fail'
        );

        $purchaseSummary->setTransaction($userCommands->getId());

        $form = $this->createForm(PurchaseSummaryType::class, $purchaseSummary);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numero = $form->get('telephone')->getData();
            $addressUser = new AddressUser((int)$numero, $userCommands->getUser()->getFullname());

            $purchaseSummary->setAddressUser($addressUser);

            $paymentProvider = $this
                                    ->payInContext
                                    ->getPaymentProvider($provider);

            $response = $paymentProvider->purchaseSummary($purchaseSummary)->payIn();

            $messageConfirmation = "/OK start : en attente de confirmation SMS/";
            $messageStartSuccess = "/OK start : versement effectué/";
            $messageFail = "/KO start :/";

            if (preg_match($messageStartSuccess, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('success', $response);
                return new RedirectResponse($this->router->generate('user_personal_command_list'));
            }

            if (preg_match($messageFail, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', $response);

                return new RedirectResponse($this->router->generate('user_cart_create'));
            }

            if (preg_match($messageConfirmation, $response)) {
                return new RedirectResponse(
                    $this->router->generate(
                        'send_sms_confirmation',
                        [
                            'provider' => $request->attributes->get('_route_params')['provider'],
                            'operateur' => $request->attributes->get('_route_params')['operateur'],
                            'telephone' => $numero,
                            'id' => $request->attributes->get('_route_params')['id']
                        ]
                    )
                );
            }
        }

        return $this->render('back/webcontroller/payment/payment.html.twig', [
            'form' => $form->createView(),
            'cart' => $userCommands,
            'operateur' => $operateur
        ]);
    }

    #[Route('/{provider}/{operateur}/{telephone}/{id}/send/sms/confirmation', name: 'send_sms_confirmation', methods: ['POST', 'GET'])]
    public function sendSMSConfirmation(string $provider, int $operateur, int $telephone, UserCommands $userCommands, Request $request)
    {
        $sendSMSPayment = new SendSMSPayment($telephone, $userCommands);

        $form = $this->createForm(SendSMSPaymentType::class, $sendSMSPayment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rcs = $form->get('rcs')->getData();

            /** @var PayInInterface $paymentProvider */
            $paymentProvider = $this
                                    ->payInContext
                                    ->getPaymentProvider($provider);

            $response = $paymentProvider->setTelephone($telephone)->payIn($rcs);
		  
		  //dump($response);

            $messageCfrmsmsSuccess = "/OK start :/";
            $messageFail = "/KO start :/";

            if (preg_match($messageCfrmsmsSuccess, $response)) {
			  
			  $status = $this->manager->getConnection()->isConnected() ? " Connected " : " Not Connected ";
		
				file_put_contents($file, PHP_EOL . $status.$response, FILE_APPEND);
			  
                $this->requestStack->getSession()->getFlashBag()->add('success', $response);
                return new RedirectResponse($this->router->generate('user_personal_command_list'));
            }

            if (preg_match($messageFail, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', $response);
			  
			  	$status = $this->manager->getConnection()->isConnected() ? " Connected " : " Not Connected ";
		
				file_put_contents($file, PHP_EOL . $status.$response, FILE_APPEND);


                return new RedirectResponse($this->router->generate('user_cart_create'));
            }
        }

        return $this->render('back/webcontroller/payment/send_sms_confirmation.html.twig', [
            'form' => $form->createView(),
            'cart' => $userCommands,
            'operateur' => $operateur
        ]);
    }

    #[Route('/{provider}/{operateur}/{telephone}/send-sms/confirmation/subscription', name: 'send_sms_confirmation_membership_subsqcription', methods: ['POST', 'GET'])]
    public function sendSMSConfirmationSubscriptionPayment(string $provider, int $operateur, int $telephone, Request $request)
    {
        $total = 0;
        $summary = [];

        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var MembershipSubscription[]|null $userSubscriptionCommands */
        $userSubscriptionCommands = $this->manager->getRepository(MembershipSubscription::class)->findBy(['createdBy' => $user, 'paid' => false]);

        foreach ($userSubscriptionCommands as $userSubscriptionCommand) {
            $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['quantity']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] + 1 : 1 ;
            $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['price']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] + $userSubscriptionCommand->getPrice() : $userSubscriptionCommand->getPrice() ;

            $total += $userSubscriptionCommand->getPrice();
        }

        $sendSMSPayment = new SendSMSPayment($telephone);

        $form = $this->createForm(SendSMSPaymentType::class, $sendSMSPayment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rcs = $form->get('rcs')->getData();

            /** @var PayInInterface $paymentProvider */
            $paymentProvider = $this
                                    ->payInContext
                                    ->getPaymentProvider($provider);

            $response = $paymentProvider->setTelephone($telephone)->payIn($rcs);

            $messageCfrmsmsSuccess = "/OK start :/";
            $messageFail = "/KO start :/";

            if (preg_match($messageCfrmsmsSuccess, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('success', $response);
                return new RedirectResponse($this->router->generate('personal_membership_subscription'));
            }

            if (preg_match($messageFail, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', $response);

                return new RedirectResponse($this->router->generate('summary_subscription_cart'));
            }
        }

        return $this->render('back/webcontroller/payment/send_sms_confirmation_membership_subscription.html.twig', [
            'form' => $form->createView(),
            'summaries' => $summary,
            'total' => $total,
            'operateur' => $operateur
        ]);
    }


    #[Route('/{provider}/{operateur}/confirm/subscription/payment', name: 'confirm_payment_subscriptions', requirements: ['provider' => '\w+', 'operateur' => '\d+'])]
    public function payPackSubscription(string $provider, int $operateur, Request $request)
    {
        $total = 0;
        $summary = [];
        $transaction = "";
        $i = 0;
        $name = "";

        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var MembershipSubscription[]|null $userSubscriptionCommands */
        $userSubscriptionCommands = $this->manager->getRepository(MembershipSubscription::class)->findBy(['createdBy' => $user, 'paid' => false]);

        foreach ($userSubscriptionCommands as $userSubscriptionCommand) {
            $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['quantity']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] + 1 : 1 ;
            $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['price']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] + $userSubscriptionCommand->getPrice() : $userSubscriptionCommand->getPrice() ;

            $total += $userSubscriptionCommand->getPrice();

            if ($i === 0) {
                $transaction = $userSubscriptionCommand->getId();
                $name = $userSubscriptionCommand->getMember()->getFullname();
            } else {
                $transaction .= '_'.$userSubscriptionCommand->getId();
            }

            $i++;
        }

        $purchaseSummary = new PurchaseSummary(
            $total,
            "Achat pack de souscription",
            $provider,
            (int)$operateur,
            'payment_subscription_success',
            'command_membership_subscription_payment_notify',
            'payment_subscription_fail'
        );

        $purchaseSummary->setTransaction($transaction);

        $form = $this->createForm(PurchaseSummaryType::class, $purchaseSummary);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numero = $form->get('telephone')->getData();
            $addressUser = new AddressUser((int)$numero, $name);

            $purchaseSummary->setAddressUser($addressUser);

            $paymentProvider = $this
                                    ->payInContext
                                    ->getPaymentProvider($provider);

            $response = $paymentProvider->purchaseSummary($purchaseSummary)->payIn();

            $messageConfirmation = "/OK start : en attente de confirmation SMS/";
            $messageStartSuccess = "/OK start : versement effectué/";
            $messageFail = "/KO start :/";

            if (preg_match($messageStartSuccess, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('success', $response);
                return new RedirectResponse($this->router->generate('personal_membership_subscription'));
            }

            if (preg_match($messageFail, $response)) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', $response);

                return new RedirectResponse($this->router->generate('summary_subscription_cart'));
            }

            if (preg_match($messageConfirmation, $response)) {
                return new RedirectResponse(
                    $this->router->generate(
                        'send_sms_confirmation_membership_subsqcription',
                        [
                            'provider' => $request->attributes->get('_route_params')['provider'],
                            'operateur' => $request->attributes->get('_route_params')['operateur'],
                            'telephone' => $numero
                        ]
                    )
                );
            }
        }

        return $this->render('back/webcontroller/payment/subscription_payment.html.twig', [
            'form' => $form->createView(),
            'summaries' => $summary,
            'operateur' => $operateur,
            'total' => $total
        ]);
    }

    #[Route('/order-membership-subscription/notify', name: 'command_membership_subscription_payment_notify', methods: ['POST', 'GET'])]
    public function notifyPaymentMembershipSubscription(Request $request)
    {
        $rI = $request->get('rI');
        $rMt = $request->get('rMt');
        $rDvs = $request->get('rDvs');
        $idReqDoh = $request->get('idReqDoh');
        $rH = $request->get('rH');
        $mode = $request->get('mode');
        $motif = $request->get('motif');
        $hash = $request->get('hash');

        $newHash = md5($idReqDoh.$rI.$rMt.$this->hashCode);

        if ($newHash != $hash) {
            return new JsonResponse('KO : Origine de la requête douteuse.');
        }

        if ($rH != $this->apiKeyDohone) {
            return new JsonResponse('KO : code marchand erroné.');
        }

        if (count(explode('_', $rI)) == 1) {
            $membershipSubscription = $this->repositorySubscription->find((int)$rI);

            if (!$membershipSubscription) {
                return new JsonResponse('KO : Commande de pack de souscription inexistante.');
            }

            $price = $membershipSubscription->getPrice();

            if (!$membershipSubscription->isPaid() && $price == $rMt) {
                if ($membershipSubscription->isUpgraded()) {
                    $membershipSubscription->setPaid(true);
                    $membershipSubscription->getMember()->setToUpgrade(true);
                    $membershipSubscription
                                            ->getMember()
                                            ->setNextMembership(
                                                $membershipSubscription->getMembership()
                                            );

                    $mbshipSubscriptionEvent = new MembershipSubscriptionActivatedEvent(
                        $membershipSubscription
                    );

                    $this->dispatcher->dispatch($mbshipSubscriptionEvent);
                } else {
                    $referralBonusEvent = new ReferralBonusEvent($membershipSubscription->getMember());
                    $membershipSubscription->getMember()->setActivated(true);

                    $dateActivation =  new DateTime(
                        "now",
                        new DateTimeZone("Africa/Douala")
                    );

                    /** @var DateTime $dateActivation */
                    $dateActivation = $this->computeDateOperation->getDate($dateActivation);

                    $membershipSubscription->getMember()->setDateActivation($dateActivation);

                    $membershipSubscription->getMember()->setCodeDistributor($this->codeDistributor->generateCode($membershipSubscription->getMember()));

                    $this->dispatcher->dispatch($referralBonusEvent);
                }

                $this->manager->flush();

                return new JsonResponse("OK start : versement effectué. REF: " . $idReqDoh);
            }

            return new JsonResponse('KO : Commande de pack de souscription erronée');
        } else {
            $membership_subscriptions = array_map(function ($id) {
                return (int)$id;
            }, explode('_', $rI));

            $membershipSubscriptions = $this
                                        ->repositorySubscription
                                        ->getListMemberSubscriptions(
                                            $membership_subscriptions
                                        );

            if (!$membershipSubscriptions) {
                return new JsonResponse('KO: Transaction inexistante');
            }

            $price = 0;
            $paid = false;

            foreach ($membershipSubscriptions as $membershipSubscription) {
                $price += $membershipSubscription->getPrice();
                if ($membershipSubscription->isPaid()) {
                    $paid = true;
                }
            }

            if (!$paid && $price == $rMt) {
                foreach ($membershipSubscriptions as $membershipSubscription) {
                    if ($membershipSubscription->isUpgraded()) {
                        $membershipSubscription->setPaid(true);
                        $membershipSubscription->getMember()->setToUpgrade(true);
                        $membershipSubscription
                                                ->getMember()
                                                ->setNextMembership(
                                                    $membershipSubscription->getMembership()
                                                );

                        $mbshipSubscriptionEvent = new MembershipSubscriptionActivatedEvent(
                            $membershipSubscription
                        );

                        $this->dispatcher->dispatch($mbshipSubscriptionEvent);
                    } else {
                        $referralBonusEvent = new ReferralBonusEvent($membershipSubscription->getMember());
                        $membershipSubscription->getMember()->setActivated(true);

                        $dateActivation =  new DateTime(
                            "now",
                            new DateTimeZone("Africa/Douala")
                        );

                        /** @var DateTime $dateActivation */
                        $dateActivation = $this->computeDateOperation->getDate($dateActivation);

                        $membershipSubscription->getMember()->setDateActivation($dateActivation);

                        $membershipSubscription->getMember()->setCodeDistributor($this->codeDistributor->generateCode($membershipSubscription->getMember()));

                        $this->dispatcher->dispatch($referralBonusEvent);
                    }
                }

                $this->manager->flush();

                return new JsonResponse("OK start : versement effectué. REF: " . $idReqDoh);
            }

            return new JsonResponse('KO start : Commande de pack de souscription erronée');
        }
    }


    #[Route('/order-product/notify', name: 'command_payment_notify', methods: ['POST', 'GET'])]
    public function notifyCommandProduct(Request $request)
    {
        $rI = $request->get('rI');
        $rMt = $request->get('rMt');
        $rDvs = $request->get('rDvs');
        $idReqDoh = $request->get('idReqDoh');
        $rH = $request->get('rH');
        $mode = $request->get('mode');
        $motif = $request->get('motif');
        $hash = $request->get('hash');
	  
	  	$file = $this->parameterBag->get('kernel.project_dir').'/public/notify_prod.log';
	  
	  	$data = <<<EOD
idVers: $idReqDoh
montant: $rMt
idTrans: $rI
hash code: $this->hashCode
code marchand fourni: $this->apiKeyDohone
code marchand reçu: $rH
hash : $hash
EOD;

	  	$data = mb_convert_encoding($data, 'UTF-8', 'OLD-ENCODING');
		file_put_contents($file, PHP_EOL . $data, FILE_APPEND);
	  
        $newHash = md5($idReqDoh.$rI.$rMt.$this->hashCode);

        if ($newHash != $hash) {
            return new JsonResponse('KO : Origine de la requête douteuse.');
        }
	  
	  
	  	$msgCode = "OK : Origine de la requête valide.";
	  
	  	file_put_contents($file, PHP_EOL . $msgCode, FILE_APPEND);
        if ($rH != $this->apiKeyDohone) {
            return new JsonResponse('KO : code marchand erroné.');
        }
	  
	  	$msgCode = "OK : Code marchand valide.";
	  
	  	file_put_contents($file, PHP_EOL . $msgCode, FILE_APPEND);

        $userCommands = $this->userCommandsRepository->find((int)$rI);

        if (!$userCommands) {
            return new JsonResponse('KO : Commande inexistante.');
        }
	  
	  	$msgCode = "OK : Code Commande valide.";
	  
	  	file_put_contents($file, PHP_EOL . $msgCode, FILE_APPEND);

        $montant = $userCommands->isDistributor() ? $userCommands->getTotalDistributorPrice() : $userCommands->getTotalClientPrice();

        if (!$userCommands->isPaid() && $montant == $rMt) {
            $userCommands->setStatus(UserCommands::STATUS_ORDERED);
            $userCommands->setPaid(true);
            $userCommands->setDateCommand(new DateTime("now", new DateTimeZone("Africa/Douala")));

            $this->manager->flush();
		  
		  	$msgCode = "OK start : versement effectué. REF: " . $idReqDoh;
	  
	  		file_put_contents($file, PHP_EOL . $msgCode, FILE_APPEND);

            return new JsonResponse("OK start : versement effectué. REF: " . $idReqDoh);
        }
	  //return new JsonResponse('KO : Commande erronée');
    }
  
    #[Route('/payment-product/success', name: 'payment_product_success', methods: ['GET'])]
    public function successPaymentProduct()
    {
        return $this->render('back/webcontroller/payment/payment_product_success.html.twig', [
            'message' => 'Merci pour la confiance que vous nous accordez'        
            ]);
    }

    #[Route('/payment-product/fail', name: 'payment_product_fail', methods: ['GET'])]
    public function failPaymentProduct()
    {
        return $this->render('back/webcontroller/payment/payment_product_fail.html.twig', [
            'message' => 'Désolé pour l\'échec de l\'opération. Vérifiez votre solde et ressayez. Merci'        ]);
    }

    #[Route('/payment-subscription/success', name: 'payment_subscription_success', methods: ['GET'])]
    public function successPaymentSubscription()
    {
        return $this->render('back/webcontroller/payment/payment_subscription_success.html.twig', [
            'message' => 'Merci pour la confiance que vous nous accordez'
        ]);
    }

    #[Route('/payment-subscription/fail', name: 'payment_subscription_fail', methods: ['GET'])]
    public function failPaymentSubscription()
    {
        return $this->render('back/webcontroller/payment/payment_subscription_fail.html.twig', [
            'message' => 'Désolé pour l\'échec de l\'opération. Vérifiez votre solde et ressayez. Merci'        ]);
    }

}
