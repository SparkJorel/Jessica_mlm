<?php

namespace App\Controller\Back\WebController;

use App\Entity\LevelBonusGenerationnel;
use App\Entity\Product;
use App\Entity\ProductDistributorPrice;
use App\Repository\ProductDistributorPriceRepository;
use App\Services\ModelHandlers\LevelBonusGenerationnelHandler;
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

class LevelBonusGenerationnelController
{
    /**
     * @var LevelBonusGenerationnelHandler
     */
    private $handler;

    public function __construct(LevelBonusGenerationnelHandler $handler)
    {
        $this->handler = $handler;
    }

    #[Route('/level-bonus-generationnels', name: 'level_bonus_generationnel_list', methods: ['GET'])]
    public function list()
    {
        return $this->handler->setEntity((new LevelBonusGenerationnel()))->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/level-bonus-generationnels/new', name: 'level_bonus_generationnel_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return $this->handler->setEntity((new LevelBonusGenerationnel()))->save($request);
    }


    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/level-bonus-generationnels/{id}/edit', name: 'level_bonus_generationnel_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, LevelBonusGenerationnel $level)
    {
        return $this->handler->setEntity($level)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/level-bonus-generationnels/{id}/delete', name: 'level_bonus_generationnel_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, LevelBonusGenerationnel $levelBonusGenerationnel)
    {
        return  $this->handler->setEntity($levelBonusGenerationnel)->remove($request, $csrf);
    }
}
