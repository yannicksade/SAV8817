<?php

namespace APM\VenteBundle\Form;

use APM\VenteBundle\Form\Type\TransactionPromptType;
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
                'required' => true
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
