<?php

namespace App\AbstractModel;

interface EntityInterface
{
    public function toString(): string;
    public function isNew() : bool;
    public function getId() : ?int;
}
