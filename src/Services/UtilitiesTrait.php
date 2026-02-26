<?php

namespace App\Services;

use App\Entity\FiltreCycle;
use App\Form\FiltreCycleType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Common features needed in some Services
 *
 * @author Patrick GUENGANG <patrickguengang@gmail.com>
 *
 * @property FormFactoryInterface $formFactory
 *
 * Trait UtilitiesTrait
 * @package App\Services
 */
trait UtilitiesTrait
{
    /**
     * @return FormInterface
     */
    protected function createForm(): FormInterface
    {
        $filtreCycle = new FiltreCycle();
        return $this->formFactory->create(FiltreCycleType::class, $filtreCycle);
    }
}
