<?php

namespace App\Form;

use App\Entity\User;
use App\Form\EventListener\AddFieldPackSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{
    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'trim' => true
            ])
            ->add('fullname', TextType::class, [
                'label' => 'Nom & prénoms',
                'required' => true,
                'trim' => true,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo',
                'required' => false,
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
            ->add('cni', IntegerType::class, [
                'required' => true,
                'label' => 'N° CNI/Passeport/Autre',
                'trim' => true,
            ])
            ->add('mobilePhone', TextType::class, [
                'required' => true,
                'label' => 'Téléphone',
                'trim' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'trim' => true,
            ])
            ->add('nextOfKin', TextType::class, [
                'label' => 'Successeur',
                'required' => false,
                'trim' => true,
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => true,
                'trim' => true,
                'placeholder' => 'Select your country',
            ])
            ->add('title', ChoiceType::class, [
                'label' => 'Titre',
                'choices' => [
                    'M.' => 'M.',
                    'Mme' => 'Mme',
                    'Mlle' => 'Mlle',
                    'Dr' => 'Dr',
                    'Hon.' => 'Hon.',
                    'Pr' => 'Pr',
                ],
                'placeholder' => 'Make a choice',
                'required' => true,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'F' => 'F',
                    'M' => 'M',
                ],
                'placeholder' => 'Make a choice',
                'required' => true,
            ])
            ->add('documentType', ChoiceType::class, [
                'label' => 'Type de document',
                'choices' => [
                    'CNI' => 'CNI',
                    'Passeport' => 'Passeport',
                    'Autres' => 'Autres',
                ],
                'placeholder' => 'Make a choice',
                'required' => true,
            ])
            ->add('dateOfBirth', BirthdayType::class, [
                'label' => 'Date de Naissance',
                'required' => true,
                'widget' => 'single_text'
            ]);

        $builder->addEventSubscriber(new AddFieldPackSubscriber($this->requestStack));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_user_profile_form_token',
            'validation_groups' => ['update_profile']
        ]);
    }
}
