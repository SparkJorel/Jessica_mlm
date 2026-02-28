<?php

namespace App\Controller\Back\WebController;

use App\Manager\CartUserLoggedManager;
use App\Entity\CommandProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CartNotificationController extends AbstractController
{
    /**
     * @var CartUserLoggedManager
     */
    private $cartUserLoggedManager;

    public function __construct(CartUserLoggedManager $cartUserLoggedManager)
    {
        $this->cartUserLoggedManager = $cartUserLoggedManager;
    }

    #[Route('/unpaid-command', name: 'unpaid_command', options: ['expose' => true])]
    public function unpaidCommand(): JsonResponse
    {
        $userCommands = $this->cartUserLoggedManager->getCurrentCart();

        if ($userCommands->getProducts()->isEmpty()) {
            return new JsonResponse([
                'count' => 0,
                'products' => []
            ]);
        }

        $cps = [];
        $total = 0;

        /**
         * @var CommandProducts $product
         */
        foreach ($userCommands->getProducts() as $product) {
            if ($product->getProduct()) {
                $cps[$product->getProduct()->getCode()] = $product->getQuantity();
                $total += $product->getQuantity(); 
            }
        }

        return new JsonResponse([
            'count' => $total,
            'products' => $cps
        ]);
    }
}
