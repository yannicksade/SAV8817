<?php

namespace APM\MarketingDistribueBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Conseiller_boutiqueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('boutique', EntityType::class, array(
                'class' => 'APMVenteBundle:Boutique',
                'choice_label' => 'code',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\MarketingDistribueBundle\Entity\Conseiller_boutique'
        ));
    }


}
