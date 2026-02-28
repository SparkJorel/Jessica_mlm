<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniquePackProductNameSetting extends Constraint
{
    public $message = "Une valeur de ce produit pour ce pack existe dejà dans la plateforme";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
