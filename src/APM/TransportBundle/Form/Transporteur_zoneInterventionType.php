<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 02/04/2017
 * Time: 22:37
 */

namespace APM\TransportBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Transporteur_zoneInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transporteur')
            ->add('zoneIntervention');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'APM\TransportBundle\Entity\Transporteur_zoneintervention'));
    }

}