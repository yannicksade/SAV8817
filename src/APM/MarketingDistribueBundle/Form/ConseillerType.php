<?php

namespace APM\MarketingDistribueBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConseillerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('dateEnregistrement', DateTimeType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('conseillerA2', CheckboxType::class, ['required' => false])
            ->add('matricule', TextType::class, ['required' => false])
            ->add('valeurQuota', NumberType::class, ['required' => false])
            ->add('utilisateur')
            ->add('reseau', EntityType::class, [
                'class' => 'APMMarketingReseauBundle:Reseau_conseillers',
                'choice_label' => 'designation',
                'required' => false
            ])
//            ->add('conseillerBoutiques', EntityType::class, [
//                'class'=>'APMMarketingDistribueBundle:Conseiller_boutique',
//                'choice_label'=>'code',
//                'required'=>false
//            ])

            ->add('reset', ResetType::class)
        ;
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
}
