<?php

namespace APM\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Groupe_relationnelType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('designation', TextType::class)
            ->add('description', TextareaType::class, ["required" => false])
            ->add('type', ChoiceType::class, array(
                'choices' => [
                    'CLIENT' => 0,
                    'FOURNISSEUR' => 1,
                    'GERANT' => 2,
                    'LIVREUR' => 3,
                    'PROSPER' => 4,
                    'CONSEILLER' => 5,
                    'CONCURRENT' => 6,
                    'COLLABORATEUR' => 7,
                    'Autre' => 8
                ]))
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\UserBundle\Entity\Groupe_relationnel'
        ));
    }
}
