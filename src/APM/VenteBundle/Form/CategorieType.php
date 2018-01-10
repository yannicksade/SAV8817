<?php

namespace APM\VenteBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    private $boutique_id;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$categorie = $builder->getData();
        $this->boutique_id = $categorie->getBoutique();*/
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
            //liste les catégories d'une boutique
            ->add('categorieCourante', EntityType::class, [
                'label' => 'Catégorie de base',
                'required' => false,
                'placeholder' => 'aucune',
                'class' => 'APMVenteBundle:Categorie',
                /*'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.boutique = :btq_id')
                        ->setParameter('btq_id', $this->boutique_id)
                        ->orderBy('c.designation', 'ASC');
                }*/
            ]);
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
