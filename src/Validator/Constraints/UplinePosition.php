<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UplinePosition extends Constraint
{
    public const POSITION_ALREADY_TAKEN = '23bd9dbf-a99e-4844bcf3077f';
    public $message = 'The position {{ side }} of the upline {{ username }} is already used.';
    public $em = null;
    public $errorPath = 'position';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
