<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\UpdateCartProductNotificationRepository::class)]
class UpdateCartProductNotification extends Notification
{
    #[ORM\ManyToOne(targetEntity: UserCommands::class)]
    private $command;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $user;


    /**
     * @return UserCommands|null
     */
    public function getCommand(): ?UserCommands
    {
        return $this->command;
    }

    /**
     * @param UserCommands|null $command
     * @return UpdateCartProductNotification
     */
    public function setCommand(?UserCommands $command): self
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return UpdateCartProductNotification
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
