<?php

namespace APM\VenteBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$groups=new Categorie();
        $builder
            ->add('designation', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('livrable', CheckboxType::class, [
                'required' => false
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => array(
                    'VENTE NORMALE' => 0,
                    'VENTE AUX ENCHERES' => 1,
                    'VENTE EN SOLDE' => 2,
                    'VENTE RESTREINTE' => 3
                ),
                //'placeholder' => 'choisir le mode de vente>>'
            ])
            ->add('image', UrlType::class, ['required' => false])

            ->add('categorieCourante', EntityType::class, [
                'class' => 'APMVenteBundle:Categorie',
                'choice_label' => 'designation',
                'required' => false
            ])
           ->add('boutique', EntityType::class, [
               'class' => 'APMVenteBundle:Boutique',
               'choice_label' => 'designation',
                'required' =>false
           ])
        ;
//        $builder->get('categorieCourante')->addModelTransformer(
//        new ObjectToStringTransformer());
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Categorie'
        ));
    }

}
