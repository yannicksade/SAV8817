<?php

namespace APM\AchatBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Service_apres_venteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En panne' => 0,
                    'Problème résolu' => 1,
                    'En cours de diagnostic' => 2,
                    'En cours de depannage' => 3,
                    'Déclaré hors service' => 4,
                    'requête à suivre' => 5,
                    'Frais exigible' => 6,
                    'Demande réjeté' => 7,
                    'Alerte' => 8,
                ]
            ])
            ->add('code')
            ->add('offre', EntityType::class, [
                'placeholder' => '',
                'class' => 'APMVenteBundle:Offre',
                'choice_name' => 'id',
                'choice_label' => 'designation',
                'attr' => ['class' => 'js-data-example-ajax form-control'],
            ])
            ->add('descriptionPanne', TextareaType::class, ['required' => true,]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\AchatBundle\Entity\Service_apres_vente'
        ));
    }
}
