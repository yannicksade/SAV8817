<?php

namespace APM\AchatBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Specification_achatType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('demandeRabais', CheckboxType::class, ['required' => false])
            ->add('livraison', CheckboxType::class, [
                'label' => 'Livrable ?',
                'required' => false
            ])
            ->add('dateLivraisonSouhaite', DateTimeType::class)
            ->add('avis', TextareaType::class, ['required' => false])
            ->add('echantillon')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\AchatBundle\Entity\Specification_achat'
        ));
    }
}
