<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConformStartedAtDatePromotionValidator extends ConstraintValidator
{
    public function validate($startedAt, Constraint $constraint)
    {
        if (!$constraint instanceof ConformStartedAtDatePromotion) {
            throw new UnexpectedTypeException($constraint, ConformStartedAtDatePromotion::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $startedAt) {
            return;
        }

        $startedAt = $startedAt->format('Y-m-d H:i:s');

        if (!preg_match('/^24-[0-9]{2}-[0-9]{4} 15:00:00$/', $startedAt, $matches)) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ started_at }}', $startedAt)
                ->addViolation();
        }
    }
}
