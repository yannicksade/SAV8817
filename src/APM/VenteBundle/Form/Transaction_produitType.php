<?php

namespace APM\VenteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class Transaction_produitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reference')
            ->add('quantite', NumberType::class, ['required' => false])
            ->add('produit', EntityType::class, [
                'class' => 'APMVenteBundle:Offre',
                'choice_label' => 'designation',
                'multiple' => true,
                'required' => true
            ])
            ->add('rabais', EntityType::class, [
                'class' => 'APMVenteBundle:Rabais_offre',
                'choice_label' => 'code',
                'required' => false
            ])

            ->add('transaction', TransactionType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Transaction_produit'
        ));
    }
}
