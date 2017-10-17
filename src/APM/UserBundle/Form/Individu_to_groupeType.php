<?php

namespace APM\UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Individu_to_groupeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('individu')
            ->add('propriete', ChoiceType::class, array(
                'choices' => array(
                    'A SUIVRE' => 0,
                    'A CONTACTER' => 1,
                    'Autre action' => 2,
                    'Administrateur' => 3,
                    'Membre' => 4,
                )))
            ->add('groupeRelationnel', EntityType::class, [
                'class' => 'APMUserBundle:Groupe_relationnel',
                'required' => true
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\UserBundle\Entity\Individu_to_groupe'
        ));
    }
}
