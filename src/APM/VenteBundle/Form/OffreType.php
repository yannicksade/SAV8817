<?php

namespace APM\VenteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('garantie', CheckboxType::class, ['required' => false])
            ->add('dataSheet', UrlType::class, [
                'default_protocol' => 'ftp',
                'required' => false
            ])
            ->add('credit', NumberType::class, ['required' => false])
            ->add('dateCreation', DateTimeType::class)
            ->add('dateExpiration', DateTimeType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('disponibleEnStock', CheckboxType::class, array(
                'required' => false
            ))
            ->add('publiable', CheckboxType::class, array(
                'required' => false
            ))

            ->add('designation', TextType::class, ['required' => true])
            ->add('dateFinGarantie', DateTimeType::class, ['required' => false])
            ->add('retourne', CheckboxType::class, ['required' => false])
            ->add('etat', ChoiceType::class, array(
                'choices' => array(
                    'NEUF' => 0,
                    'OCCASION' => 1
                )
            ))
            ->add('image', UrlType::class, [
                'default_protocol' => 'ftp',
                'required' => false
            ])
            ->add('modeVente', ChoiceType::class, [
                'choices' => array(
                    'VENTE NORMALE' => 0,
                    'VENTE AUX ENCHERES' => 1,
                    'VENTE EN SOLDE' => 2,
                    'VENTE RESTREINTE' => 3,
                ),
                // 'required' => false,
                //'placeholder' => 'choisir le mode de vente'
            ])
            ->add('numeroDeSerie', NumberType::class, ['required' => false])
            ->add('prixUnitaire', MoneyType::class, [
                'currency' => 'XAF',
                'required' => false
            ])
            ->add('quantite', NumberType::class, ['required' => false])
            ->add('evaluation', NumberType::class, ['required' => false])
            ->add('reference', TextType::class, ['required' => false])
            ->add('remiseProduit', PercentType::class, [
                'type' => 'integer',
                'scale' => 2,
                'required' => false
            ])
            ->add('typeOffre', ChoiceType::class, [
                'choices' => [
                    'ARTICLE' => 0,
                    'PRODUIT' => 1,
                    'SERVICE' => 2
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Offre'
        ));
    }
}
