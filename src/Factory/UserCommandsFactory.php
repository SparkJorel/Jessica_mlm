<?php

namespace App\Factory;

use App\Entity\CommandProducts;
use App\Entity\Product;
use App\Entity\UserCommands;
use DateTimeImmutable;
use DateTimeZone;

class UserCommandsFactory
{
    public function createUserCommands(): UserCommands
    {
        $userCommands = new UserCommands();

        return $userCommands
                    ->setStatus(UserCommands::STATUS_CART)
                    ->setMotif("Achat")
                    ->setDateCommand(new DateTimeImmutable("now", new DateTimeZone("Africa/Douala")))
                    ->setDateCommandUpdate(new DateTimeImmutable("now", new DateTimeZone("Africa/Douala")))
                    ;
    }

    public function createCommandProducts(Product $product, int $quantity = 1): CommandProducts
    {
        $commandProduct = new CommandProducts();

        return $commandProduct
                        ->setProduct($product)
                        ->setQuantity($quantity);
    }
}
