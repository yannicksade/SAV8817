<?php

namespace APM\VenteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinataireNonAvm', TextType::class, ['required' => false])
            ->add('montant', MoneyType::class, [
                'required' => false,
                'currency' => 'XAF',
                'grouping' => true
            ])
            ->add('nature', ChoiceType::class, [
                'choices' => array(
                    'ACHAT' => 0,
                    'CESSION' => 1,
                    'TRANSFERT' => 2,
                    'DONATION' => 3,
                    'LOCATION' => 4,
                    'Autre' => 5
                )
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => array(
                    'TERMINEE OK' => 0,
                    'TERMINEE KO' => 1,
                    'EN COURS' => 2,
                    'SUSPENDUE' => 3,
                    'EN ATTENTE' => 4,
                    'ANNULEE' => 5
                )
            ])
            ->add('beneficiaire', EntityType::class, [
                'class' => 'APMUserBundle:Utilisateur_avm'
            ])
            ->add('livraison', EntityType::class, [
                'class' => 'APMTransportBundle:Livraison',
                'choice_label' => 'code',
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Transaction'
        ));
    }
}
