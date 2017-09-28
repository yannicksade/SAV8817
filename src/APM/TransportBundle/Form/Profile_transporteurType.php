<?php

namespace APM\TransportBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Profile_transporteurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matricule')
            /*->add('transporteurZones', EntityType::class, [
                'class' => 'APMTransportBundle:Zone_intervention',
                'choice_label' => 'code',
                'multiple' => true,
                'required' => false
            ])*/
            ->add('zones', EntityType::class, [
                'class' => 'APMTransportBundle:Zone_intervention',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\TransportBundle\Entity\Profile_transporteur'
        ));
    }
}
