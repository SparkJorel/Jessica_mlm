<?php

namespace App\Services;

use App\Entity\CommandProducts;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserCommands;
use Doctrine\Common\Collections\Collection;
use DateTimeInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SaveUserCommand
{
    /** @var array */
    private $commands = [];

    /** @var string */
    private $username;

    /** @var DateTime */
    private $dateOp;

    /** @var EntityManagerInterface */
    private $manager;

    /**
     * @var ExtractSVFromCommands
     */
    private $extractSV;

    public function __construct(EntityManagerInterface $manager, ExtractSVFromCommands $extractSV)
    {
        $this->manager = $manager;
        $this->extractSV = $extractSV;
    }

    /**
     * @param array $commands
     * @return SaveUserCommand
     */
    public function setCommands(array $commands): self
    {
        $this->commands = $commands;

        return $this;
    }

    /** @return array */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param string $username
     * @return SaveUserCommand
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param DateTimeInterface|null $dateOp
     * @return SaveUserCommand
     */
    public function setDateOp(?DateTimeInterface $dateOp): self
    {
        $this->dateOp = $dateOp;

        return $this;
    }

    public function getDateOp(): ?DateTimeInterface
    {
        return $this->dateOp;
    }

    /**
     * @return false|string
     */
    private function getUsernameFromName()
    {
        $user_tab = explode(" ", $this->username);
        return substr(trim($user_tab[count($user_tab) - 1]), 1, -1);
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function getUser(string $username): ?User
    {
        /**
         * @var User|null $user
         */
        $user = $this->manager
                                ->getRepository(User::class)
                                ->findOneBy([
                                    'username' => $username
                                ]);

        return $user;
    }

    /**
     * @param string $code
     * @return Product|null
     */
    private function getProduct(string $code): ?Product
    {
        /**
         * @var Product|null $product
         */
        $product = $this->manager
                        ->getRepository(Product::class)
                        ->findOneBy(['code' => $code]);

        return $product;
    }

    /**
     * @param UserCommands $userCommand
     * @return void
     */
    private function handleCommand(UserCommands &$userCommand): void
    {
        $totalSVAP = 0;
        $totalSV = 0;

        foreach ($this->commands as $command) {
            $this->handleOneCommandLine($userCommand, $command);
        }

        /** @var CommandProducts $product */
        foreach ($userCommand->getProducts() as $product) {
            $totalSV += $product->getItemSVBinaire();
            $totalSVAP += $product->getItemSVAP();
        }

        $userCommand->setTotalSVAP($totalSVAP);
        $userCommand->setTotalSVBinaire($totalSV);
    }

    /**
     * @param UserCommands $userCommand
     * @param array $command
     * @return void
     */
    private function handleOneCommandLine(UserCommands &$userCommand, array $command): void
    {
        $product = $this->getProduct($command[0]);
        $quantity = (int)$command[1];

        $commandProduct = (new CommandProducts())
                            ->setProduct($product)
                            ->setQuantity($quantity)
                            ->setItemSVAP(
                                $this->extractSV->processSVByType($product, $quantity, true)
                            )
                            ->setItemSVBinaire(
                                $this->extractSV->processSVByType($product, $quantity)
                            );

        $this->manager->persist($commandProduct);

        $userCommand->addProduct($commandProduct);
    }

    /**
     * @param UserCommands $userCommand
     * @throws Exception
     */
    public function saveCommand(UserCommands $userCommand): void
    {
        if ($this->dateOp) {
            $userCommand->setDateCommand($this->dateOp);
        }

        $user = $this->getUser($this->getUsernameFromName());
        $userCommand->setUser($user);

        $this->handleCommand($userCommand);

        $this->manager->persist($userCommand);

        $this->manager->flush();
    }

    /**
     * @param UserCommands $userCommand
     */
    public function editCommand(UserCommands $userCommand)
    {
        $totalSV = 0;
        $totalSVAP = 0;

        $username = $this->getUsernameFromName();
        /** @var CommandProducts[]|Collection $products */
        $products = $userCommand->getProducts();

        if ($userCommand->getUser()->getUsername() != $username) {
            /**
             * @var User $user
             */
            $user = $this->getUser($username);

            $userCommand->setUser($user);
        }

        foreach ($products as $product) {
            $found = false;
            foreach ($this->commands as $command) {
                if ($product->getProduct()->getCode() == $command[0]) {
                    $found = true;
                    if ($command[1] != $product->getQuantity()) {
                        $product->setQuantity((int)$command[1]);

                        $product->setItemSVAP(
                            $this->extractSV->processSVByType(
                                $product->getProduct(),
                                (int)$command[1],
                                true
                            )
                        );

                        $product->setItemSVBinaire(
                            $this->extractSV->processSVByType(
                                $product->getProduct(),
                                (int)$command[1]
                            )
                        );
                    }
                }
            }

            if (!$found) {
                $userCommand->removeProduct($product);
                $this->manager->remove($product);
            }
        }

        foreach ($this->commands as $command) {
            $found = false;
            foreach ($products as $product) {
                if ($product->getProduct()->getCode() == $command[0]) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->handleOneCommandLine($userCommand, $command);
            }
        }

        /** @var CommandProducts $product */
        foreach ($userCommand->getProducts() as $product) {
            $totalSV += $product->getItemSVBinaire();
            $totalSVAP += $product->getItemSVAP();
        }

        $userCommand->setTotalSVAP($totalSVAP);
        $userCommand->setTotalSVBinaire($totalSV);

        $this->manager->flush();
    }
}
