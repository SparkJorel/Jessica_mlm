<?php

namespace App\Form;

use App\Entity\PrestationService;
use App\Form\EventListener\AddFieldAnalyseFonctionnelleSystematiqueSubscriber;
use App\Form\EventListener\AddServiceFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PrestationServiceType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'examen',
                'required' => true,
            ])
            ->add('code', TextType::class, [
                'label' => 'Code de l\'examen',
                'required' => true,
            ])
            ->add('cost', NumberType::class, [
                'label' => 'Coût de l\'examen',
                'required' => true,
            ])
            ->add('pourcentagePrescripteurSNLMembre', NumberType::class, [
                'label' => 'Prescripteur membre(%)',
                'required' => true,
            ])
            ->add('pourcentagePrescripteurSNL', NumberType::class, [
                'label' => 'Prescripteur SNL(%)',
                'required' => true,
            ])
            ->add('pourcentageSponsorPrescripteurSNL', NumberType::class, [
                'label' => 'Sponsor prescripteur(%)',
                'required' => true,
            ])
            ->add('binaire', NumberType::class, [
                'label' => 'Binaire',
                'required' => true,
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Activé cette prestation ?',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'label' => 'Brochure (Photo)',
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
        ;

        $builder->addEventSubscriber(new AddServiceFieldSubscriber($this->requestStack));

        $builder->addEventSubscriber(new AddFieldAnalyseFonctionnelleSystematiqueSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrestationService::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_prestation_service_form_token',
            'validation_groups' => 'registration_prestation_service',
        ]);
    }
}
