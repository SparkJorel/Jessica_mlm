<?php

namespace App\Storage;

use App\Entity\User;
use App\Entity\UserCommands;
use App\Repository\UserCommandsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CartSessionStorage
{
    public const CART_KEY_NAME = 'cart_id';

    private RequestStack $requestStack;

    /**
     * The cart repository
     *
     * @var UserCommandsRepository $cartRepository
     */
    private $cartRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        UserCommandsRepository $cartRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->cartRepository = $cartRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Gets the cart in the session.
     *
     * @return UserCommands|null
     */
    public function getCart(): ?UserCommands
    {
        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user) {
            return $this->cartRepository->getLastCommandNotPaid($user, UserCommands::STATUS_CART);
        } else {
            return $this->cartRepository->findOneBy([
                'id' => $this->getCartId(),
                'status' => UserCommands::STATUS_CART
            ]);
        }
    }

    /**
     * Sets the cart in the session
     *
     * @param UserCommands $order
     * @return void
     */
    public function setCart(UserCommands $order): void
    {
        $this->requestStack->getSession()->set(self::CART_KEY_NAME, $order->getId());
    }

    /**
     * Return the cart id.
     *
     * @return integer|null
     */
    private function getCartId(): ?int
    {
        return $this->requestStack->getSession()->get(self::CART_KEY_NAME);
    }
}
