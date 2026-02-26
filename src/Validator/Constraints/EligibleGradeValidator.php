<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EligibleGradeValidator extends ConstraintValidator
{
    public function validate($promoBonus, Constraint $constraint)
    {
        if (!$constraint instanceof EligibleGrade) {
            throw new UnexpectedTypeException($constraint, EligibleGrade::class);
        }

        if ($promoBonus->getUnderCondition() &&
            null === $promoBonus->getEligibleGrade()) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
