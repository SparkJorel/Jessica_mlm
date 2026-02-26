<?php

namespace App\Form;

use App\Entity\BonusSpecial;
use App\Entity\Grade;
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BonusSpecialType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('weight', ChoiceType::class, [
                'label' => 'Poids',
                'choices' => $this->getWeightFromGrade(),
                'choice_value' => 'weight',
                'choice_label' => function (?Grade $grade) {
                    return $grade ? strtoupper($grade->getCommercialName().' '.$grade->getWeight()) : '';
                },
                'help' => 'Ce choix doit être fonction du grade associé au bonus',
            ])
            ->add('cap1', NumberType::class, [
                'label' => 'Borne minimale'
            ])
            ->add('cap2', NumberType::class, [
                'Borne maximale'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'label' => 'Image descriptive',
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (jpeg|png)',
                    ])
                ]
            ])
            ->add('video', FileType::class, [
                'required' => false,
                'label' => 'Vidéo descriptive',
                'constraints' => [
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/webm',
                            'video/ogg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid video file (mp4|webm|ogg)',
                    ])
                ]
            ])
            ->add('grade', EntityType::class, [
                'class' => Grade::class,
                'label' => 'Grade',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository
                                ->createQueryBuilder('g')
                                ->where('g.rewardable = :rewardable')
                                ->setParameter('rewardable', true)
                                ->orderBy('g.weight', 'ASC')
                        ;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BonusSpecial::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_bonus_special_form_token',
        ]);
    }

    /**
     * @return Grade[]
     */
    private function getWeightFromGrade()
    {
        /**
         * @var GradeRepository $repository
         */
        $repository = $this->manager->getRepository(Grade::class);

        return $repository->findAll();
    }
}
