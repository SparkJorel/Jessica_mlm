<?php

namespace App\Form;

use App\Entity\SearchUser;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullname', TextType::class, [
            'label' => 'Nom de l\'utilisateur',
            'trim' => true,
            'required' => false,
            ])
            ->add('city', ChoiceType::class, [
                'label' => 'Ville',
                'choices' => $this->getCities(),
                'choice_label' => function (string $city = null) {
                    return $city ? strtoupper($city) : '--Ville--';
                },
                'choice_value' => function (string $city = null) {
                    return $city ? $city : ' ';
                },
                'trim' => true,
                'required' => false,
            ])
            ->add('recherche', SubmitType::class, [
                'label' => 'Rechercher',
                'validation_groups' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchUser::class,
            'csrf_protection' => false,
            'method' => 'get'
        ]);
    }

    private function getCities()
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        return $repository->getCities();
    }
}
