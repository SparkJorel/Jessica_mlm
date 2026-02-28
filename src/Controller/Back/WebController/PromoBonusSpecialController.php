<?php

namespace App\Controller\Back\WebController;

use App\Entity\PromoBonusSpecial;
use App\Services\ModelHandlers\PromoBonusSpecialHandler;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PromoBonusSpecialController
{
    /**
     * @var PromoBonusSpecialHandler
     */
    private $promoBonusSpecialHandler;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(PromoBonusSpecialHandler $promoBonusSpecialHandler, EntityManagerInterface $manager)
    {
        $this->promoBonusSpecialHandler = $promoBonusSpecialHandler;
        $this->manager = $manager;
    }

    #[Route('/promo-bonus-special', name: 'promo_bonus_special_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->promoBonusSpecialHandler
                ->setEntity((new PromoBonusSpecial()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/promo-bonus-special/new', name: 'promo_bonus_special_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->promoBonusSpecialHandler
                ->setEntity((new PromoBonusSpecial()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/promo-bonus-special/{id}', name: 'promo_bonus_special_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(PromoBonusSpecial $promo)
    {
        return $this->promoBonusSpecialHandler->setEntity($promo)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/promo-bonus-special/{id}/edit', name: 'promo_bonus_special_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, PromoBonusSpecial $promo)
    {
        return
            $this
                ->promoBonusSpecialHandler
                ->setEntity($promo)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/promo_bonus_special/{id}/delete', name: 'promo_bonus_special_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, PromoBonusSpecial $promo)
    {
        return  $this
            ->promoBonusSpecialHandler
            ->setEntity($promo)
            ->remove($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/promo_bonus_special/handle', name: 'handle_status_promotion', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function handleStatus(Request $request, EntityManagerInterface $manager)
    {
        $status = $request->request->get('status');
        $id = $request->request->get('id');

        /**
         * @var PromoBonusSpecial $promotionBonusSpecial
         */
        $promotionBonusSpecial = $manager
                                        ->getRepository(PromoBonusSpecial::class)
                                        ->find((int)$id);

        /** @var DateTime $currentDatetime */
        $currentDatetime = new DateTime('now', new DateTimeZone("Africa/Douala"));

        if ($currentDatetime->format('Y-m-d H:i:s') > $promotionBonusSpecial->getEndedAt()->format('Y-m-d H:i:s')) {
            return new JsonResponse([
                'status' => false
            ]);
        }

        $promotionBonusSpecial->setStatus(!$status);

        $manager->flush();

        return new JsonResponse(['status' => true]);
    }
}
