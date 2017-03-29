<?php

namespace APM\TransportBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Livreur_boutiqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class)
            //->add('transporteur', Profile_transporteurType::class)//enregistrer un livreur avec son profile transporteur
            ->add('transporteur', EntityType::class, [
                'class' => 'APM\TransportBundle\Entity\Profile_transporteur',
                'choice_label' => 'code'
            ])
            ->add('boutique', EntityType::class, [
                'class' => 'APMVenteBundle:Boutique',
                'choice_label' => 'code'
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\TransportBundle\Entity\Livreur_boutique'
        ));
    }
}
