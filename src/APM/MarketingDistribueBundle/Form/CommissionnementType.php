<?php

namespace APM\MarketingDistribueBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommissionnementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creditDepense', NumberType::class, ['required' => false])
            ->add('libelle')
            ->add('description')
            ->add('quantite', NumberType::class)
            //il ne doit s'agit que des conseillers de la boutique
            ->add('conseillerBoutique', EntityType::class, [
                'class' => 'APMMarketingDistribueBundle:Conseiller_boutique',
                'choice_label' => 'conseiller',
                'required' => true
            ])
            //il ne doit s'agit que des quotas créés par la boutique en question
            ->add('commission', EntityType::class, [
                'class' => 'APMMarketingDistribueBundle:Quota',
                'choice_label' => 'code',
                'required' => true
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\MarketingDistribueBundle\Entity\Commissionnement'
        ));
    }
}
