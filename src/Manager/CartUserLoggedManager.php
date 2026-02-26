<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\UserCommands;
use App\Storage\CartSessionStorage;
use App\Factory\UserCommandsFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CartUserLoggedManager
{
    private $manager;

    private $tokenStorage;

    private $cartSessionStorage;

    private $userCommandsFactory;

    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage, CartSessionStorage $cartSessionStorage, UserCommandsFactory $userCommandsFactory)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->cartSessionStorage = $cartSessionStorage;
        $this->userCommandsFactory = $userCommandsFactory;
    }

    public function getCurrentCart(): UserCommands
    {
        $userCommands = $this->cartSessionStorage->getCart();

        //dd($userCommands);

        if (!$userCommands) {

            /** @var User $user **/
            $user = $this->tokenStorage->getToken()->getUser();

            $userCommands = $this->userCommandsFactory->createUserCommands();
            $userCommands->setUser($user);
        }

        return $userCommands;
    }

    /**
     * Persist the cart in the database
     *
     * @param UserCommands $userCommands
     * @return void
     */
    public function save(UserCommands $userCommands)
    {
        //dd($userCommands);
	  	
	  	$userCommands->setTotalSVAP($userCommands->processSVAPTotal());
        $userCommands->setTotalSVBinaire($userCommands->processSVBinaireTotal());
        
        if ($userCommands->isDistributor()) {
            $userCommands->setTotalDistributorPrice($userCommands->getTotalDistributorWithoutShippingCost());
        } else {
            $userCommands->setTotalClientPrice($userCommands->getTotalClientWithoutShippingCost());
        }

        $this->manager->persist($userCommands);
        $this->manager->flush();
        $this->cartSessionStorage->setCart($userCommands);
    }
}
