<?php

namespace App\Storage;

use App\Entity\User;
use App\Entity\UserCommands;
use App\Repository\UserCommandsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CartSessionStorage
{
    public const CART_KEY_NAME = 'cart_id';

    /**
     * The session storage
     *
     * @var SessionInterface $session
     */
    private $session;

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
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
        UserCommandsRepository $cartRepository
    )
    {
        $this->session = $session;
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
        $this->session->set(self::CART_KEY_NAME, $order->getId());
    }

    /**
     * Return the cart id.
     *
     * @return integer|null
     */
    private function getCartId(): ?int
    {
        return $this->session->get(self::CART_KEY_NAME);
    }
}
