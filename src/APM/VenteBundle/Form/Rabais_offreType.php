<?php

namespace APM\VenteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Rabais_offreType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateLimite', DateTimeType::class)
            ->add('nombreDefois', NumberType::class, ['required' => false])
            ->add('prixUpdate', MoneyType::class, [
                'grouping' => true,
                'required' => true,
                'currency' => 'XAF'
            ])
            //->add('pourcentage', PercentType::class)
            ->add('quantiteMin', NumberType::class, ['required' => false])
            ->add('beneficiaireRabais', EntityType::class, [
                'class' => 'APMUserBundle:Utilisateur_avm',
                'choice_label' => 'username'
            ])
            ->add('offre', EntityType::class, [
                'class' => 'APMVenteBundle:Offre',
                'choice_label' => 'designation',
                'required' => false
            ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Rabais_offre'
        ));
    }
}
