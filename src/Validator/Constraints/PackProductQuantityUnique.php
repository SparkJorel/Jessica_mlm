<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PackProductQuantityUnique extends Constraint
{
    public $message = "Les doublons ne sont pas autorisés";

    public $errorPath = "product";
}
