<?php

namespace APM\AchatBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('code')
            ->add('dateDeVigueur', TextType::class, [
                'required' => false,
                /*'html5' => false,
                'widget' => 'single_text',*/
                //'format' => "dd MM yyyy - HH:ii",
                //'attr'=>['class' =>'bs-datetime'],
                //'label'=>'date alerte',
                //'label_attr' =>['class' =>'control-label']
            ])

            ->add('description', TextareaType::class, ['required' => false])
            ->add('propriete', ChoiceType::class, [
                'choices' => array(
                    'A ACHETER' => 0,
                    'A SUIVRE' => 1,
                    'A CONTACTER VENDEUR' => 2,
                    'STOCK PREVISIONNEL' => 3,
                    'A RE-VENDRE' => 4,
                    'Autres' => 5,
                ),
                'required' => true
            ])
            ->add('recurrent', CheckboxType::class, ['required' => false])
            ->add('designation', TextType::class, ['required' => true])/*->add('offres', EntityType::class, array(
                    'class' => 'APMVenteBundle:Offre',
                    'choice_label' => 'designation',
                    'required' => false,
                    'multiple' => true
                )
            )*/
        ;
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
