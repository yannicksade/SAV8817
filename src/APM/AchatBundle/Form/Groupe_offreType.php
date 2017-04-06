<?php

namespace APM\AchatBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Groupe_offreType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDeVigueur', DateTimeType::class, [
                'input' => 'datetime',
                //'by_reference'=>true
                'widget' => 'choice',
                'format' => 'dd-MM-yyyy HH:mm',
//                'date_format'=>'dd-MM-yyyy HH:mm',
//                'date_widget'=>'',
//                'time_widget'=>'',
                'invalid_message' => 'cette date n\'est pas valide',
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('propriete', ChoiceType::class, [
                'choices' => array(
                    'A ACHETER' => 0,
                    'A SUIVRE' => 1,
                    'A CONTACTER VENDEUR' => 2,
                    'STOCK PREVISIONNEL' => 3,
                    'A RE-VENDRE' => 3,
                    'Autres' => 4,
                ),
                'required' => true
            ])
            ->add('recurrent', CheckboxType::class, ['required' => false])
            ->add('designation', TextType::class, ['required' => true])
            ->add('offres', EntityType::class, array(
                    'class' => 'APMVenteBundle:Offre',
                    'choice_label' => 'designation',
                    'required' => false,
                    'multiple' => true
                )
            )
            ->add('reset', ResetType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\AchatBundle\Entity\Groupe_offre'
        ));
    }
}
