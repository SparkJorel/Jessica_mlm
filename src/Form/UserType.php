<?php

namespace App\Form;

use App\Entity\Membership;
use App\Entity\User;
use App\Form\DataTransformer\SponsorAutocompleteTransformer;
use App\Form\EventListener\AddPasswordFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $manager = $options['manager'];

        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
                'trim' => true
            ])
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
                'required' => false,
                'label' => 'N° CNI/Passeport',
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
            ->add('position', ChoiceType::class, [
                'label' => 'Position',
                'choices' => [
                    'Left' => 'Left',
                    'Right' => 'Right'
                ],
                'placeholder' => 'Select Position',
                'required' => true,
            ])
            ->add('membership', EntityType::class, [
                'class' => Membership::class,
                'choice_label' => 'code',
                'placeholder' => 'Your Package',
                'label' => 'Package',
                'required' => true,
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
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('sponsor', TextType::class, array(
                'required' => true,
                'label' => 'Sponsor',
                'trim' => true,
                'invalid_message' => 'The sponsor that you enter is not present.'
            ));
        $builder->get('sponsor')
            ->addModelTransformer(new SponsorAutocompleteTransformer($manager));

        $builder->addEventSubscriber(new AddPasswordFieldSubscriber());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($manager) {
            $user = $event->getData();
            $form = $event->getForm();

            if ($user->isNew()) {
                $form
                    ->add('upline', TextType::class, array(
                        'required' => false,
                        'label' => 'Upline',
                        'model_user_transformer' => new SponsorAutocompleteTransformer($manager),
                        'trim' => true,
                    ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_user_form_token',
            'manager' => 'Doctrine\Common\Persistence\ObjectManager',
            'validation_groups' => ['registration']
        ]);
    }
}
