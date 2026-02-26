<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Membership;
use App\Form\DataTransformer\SponsorAutocompleteTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddNewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $manager = $options['manager'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'trim' => true,
                'help' => 'cet email sera utilisé comme identifiant et mot de passe de connexion par défaut'
            ])
            ->add('fullname', TextType::class, [
                'label' => 'Nom & prénoms',
                'required' => true,
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
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => true,
                'trim' => true,
                'placeholder' => 'Sélectionner le pays',
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'Position',
                'choices' => [
                    'Left' => 'Left',
                    'Right' => 'Right'
                ],
                'placeholder' => '--Position--',
                'required' => true,
                'help' => 'Cette position est par rapport au upline choisi si le champ est renseigné'
            ])
            ->add('membership', EntityType::class, [
                'class' => Membership::class,
                'choice_label' => 'code',
                'placeholder' => 'Son pack de souscription',
                'label' => 'Package',
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
            ->add('sponsor', TextType::class, array(
                'required' => true,
                'label' => 'Sponsor',
                'trim' => true,
                'invalid_message' => 'The sponsor that you enter is not present.'
            ));
        $builder->get('sponsor')
            ->addModelTransformer(new SponsorAutocompleteTransformer($manager));

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
            'validation_groups' => ['quick_registration']
        ]);
    }
}
