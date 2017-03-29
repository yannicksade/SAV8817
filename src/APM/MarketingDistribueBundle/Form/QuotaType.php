<?php

namespace APM\MarketingDistribueBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuotaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('description', TextareaType::class, ['required' => false])
            ->add('libelleQuota', TextType::class, ['required' => false])
            ->add('valeurQuota', MoneyType::class, [
                'grouping' => true,
                'currency' => 'XAF',
                'required' => false
            ])
            ->add('boutiqueProprietaire', EntityType::class, [
                'class' => 'APMVenteBundle:Boutique',
                'choice_label' => 'code',
                'required' => true
            ])
//            ->add('commissionnements', EntityType::class, [
//                'class' => 'APMMarketingDistribueBundle:Commissionnement',
//                'choice_label' => 'code',
//                'multiple' => true,
//                'required' => false
//            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\MarketingDistribueBundle\Entity\Quota'
        ));
    }
}
