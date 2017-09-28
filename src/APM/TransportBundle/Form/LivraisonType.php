<?php

namespace APM\TransportBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivraisonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateEtHeureLivraison', DateTimeType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('etatLivraison', ChoiceType::class, array(
                'choices' => [
                    'EN ATTENTE' => 0,
                    'EN COURS' => 1,
                    'TERMINEE' => 2,
                    'SUSPENDUE' => 3,
                    'ANNULEE' => 4,
                    'Autre' => 5
                ],
                'required' => false
            ))
            ->add('priorite', ChoiceType::class, array(
                'choices' => [
                    'NORMALE' => 0,
                    'PRIVILEGIE' => 1,
                    'HAUTE' => 2,
                    'URGENCE' => 3,
                ],
                'required' => false
            ))
            ->add('operations', EntityType::class, [
                'class' => 'APMVenteBundle:Transaction',
                'choice_label' => 'code',
                'required' => true,
                'multiple' => true
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\TransportBundle\Entity\Livraison'
        ));
    }
}
