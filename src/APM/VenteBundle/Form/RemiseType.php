<?php

namespace APM\VenteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemiseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateExpiration', DateTimeType::class)
            ->add('restreint', CheckboxType::class, ['required' => false])
            ->add('permanence', CheckboxType::class, ['required' => false])
            ->add('nombreUtilisation', NumberType::class, ['required' => false])
            ->add('quantiteMin', NumberType::class, ['required' => false])
            ->add('valeur', MoneyType::class, [
                'grouping' => true,
                'required' => false])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Remise'
        ));
    }
}
