<?php

namespace APM\TransportBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('code')
            ->add('livreurBoutique', CheckboxType::class, ['required' => false])
            ->add('matricule', TextType::class)
            ->add('utilisateur')
            ->add('transporteurZones', EntityType::class, [
                'class' => 'APMTransportBundle:Zone_intervention',
                'choice_label' => 'code',
                'multiple' => true,
                'required' => false
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
