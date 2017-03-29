<?php

namespace APM\MarketingReseauBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Reseau_conseillersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('designation', TextType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('advisors', EntityType::class, [
                'class' => 'APMMarketingDistribueBundle:Conseiller',
                'choice_label' => 'code',
                'multiple' => true,
                'required' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\MarketingReseauBundle\Entity\Reseau_conseillers'
        ));
    }
}
