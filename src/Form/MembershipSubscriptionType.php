<?php

namespace App\Form;

use App\Entity\MembershipSubscription;
use App\Form\DataTransformer\SponsorAutocompleteTransformer;
use App\Form\EventListener\AddFieldMembershipSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $manager = $options['manager'];

        $builder
            ->add('member', TextType::class, array(
                'required' => false,
                'label' => 'User',
                'trim' => true,
                'invalid_message' => 'The user that you enter is not present.'
            ));
        $builder->get('member')
                ->addModelTransformer(
                    new SponsorAutocompleteTransformer($manager)
                );
        $builder->add('price', NumberType::class, [
            'label' => 'Reste à payer',
            'required' => true,
            'trim' => true,
        ]);
        $builder->addEventSubscriber(new AddFieldMembershipSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MembershipSubscription::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_mbship_sub_form_token',
            'manager' => 'Doctrine\Persistence\ObjectManager'
        ]);
    }
}
