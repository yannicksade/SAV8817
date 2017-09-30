<?php

namespace APM\MarketingReseauBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Reseau_conseillersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conseiller', EntityType::class, [
                'class' => 'APMMarketingDistribueBundle:Conseiller',
                'required' => false,
                'placeholder' => 'null'
            ])
            ->add('modification', ChoiceType::class, [
                'expanded' => true,
                'empty_data' => '0',
                'choices' => [
                    'Inserer' => 0,
                    'Remplacer' => 1,
                    'Fusionner' => 2,
                ],
            ])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'gauche' => 1,
                    'droite' => 0,
                ],
                'expanded' => true,
                'required' => true,
                'empty_data' => '1',
            ])
        ;
    }

}
