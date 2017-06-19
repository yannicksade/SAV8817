<?php

namespace APM\MarketingReseauBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('remplacer', CheckboxType::class, [
                'required' => false,
                'label' => 'Inserer/Remplacer',
            ]);
    }

}
