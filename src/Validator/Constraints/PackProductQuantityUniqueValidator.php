<?php

namespace App\Validator\Constraints;

use App\Entity\MembershipProduct;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PackProductQuantityUniqueValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PackProductQuantityUnique) {
            throw new UnexpectedTypeException($constraint, PackProductQuantityUnique::class);
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, 'Collection');
        }

        if (!is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        if (!$value) {
            return ;
        }

        $items = [];


        /** @var MembershipProduct $item  */
        foreach ($value as $item) {
            if (empty($items)) {
                $items[] = $item->getMembership()->getId().'_'. $item->getName()->getId() . $item->getProduct()->getId();
            } else {
                $token = $item->getMembership()->getId().'_'. $item->getName()->getId() . $item->getProduct()->getId();
                if (in_array($token, $items)) {
                    $this
                    ->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->errorPath)
                    ->addViolation();
                } else {
                    $items[] = $item->getMembership()->getId().'_'. $item->getName()->getId() . $item->getProduct()->getId();
                }
            }
        }
    }
}
