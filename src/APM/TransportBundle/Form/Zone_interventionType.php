<?php

namespace APM\TransportBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Zone_interventionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zoneTime', TimezoneType::class, [
                'placeholder' => 'choisir une zone time'
            ])
            ->add('language', LocaleType::class, [
                'placeholder' => 'choisir une langue'
            ])
            ->add('designation', TextType::class)
            ->add('adresse', TextType::class, [
                'label' => 'Localité',
                'required' => false,
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('pays', CountryType::class, [
                    'placeholder' => 'choisir un pays']
            )
            ->add('transporteur', EntityType::class, [
                'class' => 'APMTransportBundle:Profile_transporteur',
                'required' =>false
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\TransportBundle\Entity\Zone_intervention'
        ));
    }
}
