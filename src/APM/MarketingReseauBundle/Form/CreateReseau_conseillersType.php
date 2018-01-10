<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 10/01/2018
 * Time: 10:06
 */

namespace APM\MarketingReseauBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateReseau_conseillersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreInstanceReseau');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\MarketingDistribueBundle\Entity\Conseiller'
        ));
    }

    public function getBlockPrefix()
    {
        return "network_form";
    }
}
