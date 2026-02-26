<?php

namespace App\Form;

use App\Entity\Cycle;
use App\Entity\FiltreCycle;
use App\Entity\User;
use App\Repository\CycleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FiltreCycleType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                    ->add('period', ChoiceType::class, [
                        'label' => 'Période',
                        'placeholder' => 'Choisir une période',
                        'choices' => $this->getCycles(),
                        'choice_label' => function (Cycle $choice) {
                            return $choice ? $choice->getStartedAt()->format('d/m/Y').' '. $choice
                                    ->getEndedAt()->format('d/m/Y') : '';
                        },
                        'choice_value' => function (Cycle $choice = null) {
                            return $choice ? $choice->getId() : '';
                        },
                        'required' => false,
                    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FiltreCycle::class,
            'csrf_protection' => false,
            'method' => 'get',
        ]);
    }

    private function getCycles()
    {
        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();

        /**
         * @var CycleRepository $repository
         */
        $repository = $this->manager->getRepository(Cycle::class);

        return $repository->getCycles($user);
    }


    public function getBlockPrefix()
    {
        return '';
    }
}
