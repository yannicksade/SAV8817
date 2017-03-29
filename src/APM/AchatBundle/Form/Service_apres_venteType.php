<?php

namespace APM\AchatBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Service_apres_venteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codeSav')
            ->add('dateDue', DateTimeType::class, ['required' => false])
            ->add('descriptionPanne', TextareaType::class, ['required' => false])
            ->add('etat', ChoiceType::class, [
                'choices' => array(
                    "EN PANNE" => 0,
                    "DEPANNE" => 1,
                    "EN COURS DE DEPANNAGE" => 2,
                    "Autre" => 3
                )
            ])
            ->add('client');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\AchatBundle\Entity\Service_apres_vente'
        ));
    }
}
