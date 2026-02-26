<?php

namespace App\Form;

use App\Entity\IndirectBonusProduct;
use App\Entity\ParameterConfig;
use App\Entity\Product;
use App\Repository\ParameterConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndirectBonusProductType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class, [
                'label' => 'Valeur'
            ])
            ->add('lvl', ChoiceType::class, [
                'label' => 'Génération',
                'choices' => $this->getLevels(),
                'placeholder' => '--génération--',
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'label' => 'Produit',
                'placeholder' => '--produit--'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IndirectBonusProduct::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_jtwc_token',
            'csrf_token_id'   => 'jtwc_indirect_bonus_form_token',
            'validation_groups' => 'registration_indirect_bonus'
        ]);
    }

    private function getLevels()
    {
        /** @var ParameterConfigRepository $repository */
        $repository = $this->manager->getRepository(ParameterConfig::class);

        $parameterConfig = $repository->findOneBy(['name' => 'indirect_bonus', 'status' => true]);

        if (!$parameterConfig) {
            return [];
        }

        $levels = [];

        for ($i = 1; $i <= (int)$parameterConfig->getValue(); $i++) {
            $levels["$i"] = $i;
        }

        return $levels;
    }
}
