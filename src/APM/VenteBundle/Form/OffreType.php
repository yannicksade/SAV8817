<?php

namespace APM\VenteBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OffreType extends AbstractType
{
    //private $vendeur_id;
    //private $boutique_id;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       /* $offre = $builder->getData();
        $this->vendeur_id = $offre->getVendeur();

        $this->boutique_id = $offre->getBoutique();
        //vendeur et gerant ont

        $query = function (EntityRepository $er) {
            return $er->createQueryBuilder('b')
                ->where('b.proprietaire = :prt_id')
                ->setParameter('prt_id', $this->vendeur_id)
                ->orWhere('b.gerant = :grt_id')
                ->setParameter('grt_id', $this->vendeur_id)
                ->orderBy('b.designation', 'ASC');
        };*/

        $builder
            //recupérer uniquement les catégories des boutiques dont l'utilisateur est, en tant que gérant ou proprietaire
            ->add('id', TextType::class, [
                'mapped' => false,
                'attr' => ['class' => 'id hidden'],
                'required'=>false,
            ])
            ->add('code', TextType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control code'],
                'required'=>false,
            ])
            ->add('designation', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control designation'],
                ])
            ->add('categorie', EntityType::class,
                [
                    'class' => 'APMVenteBundle:Categorie',
                    'required' => false,
                    'attr' => ['class' => 'form-control select2 categorie'],
                    /*'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')//* pris en compte si boutique connue
                        ->leftJoin('c.boutique', 'btq')
                            ->addSelect('btq')
                            ->where('c.boutique = :btq_id')//* pris en compte si boutique connue
                            ->setParameter('btq_id', $this->boutique_id)//* pris en compte si boutique connue
                            ->orWhere('btq.gerant = :grt_id OR btq.proprietaire = :prt_id')
                            ->setParameter('grt_id', $this->vendeur_id)
                            ->setParameter('prt_id', $this->vendeur_id)
                            ->orderBy('c.designation', 'ASC');
                    }*/
                ])
            ->add('boutique', EntityType::class, [
                'class' => 'APMVenteBundle:Boutique',
                'attr' => ['class' => 'form-control select2 boutique'],
                //Affiche les boutiques propres et les boutique gérées par le vendeur pour l'affectation de l'offre qu'il créee
                /*'query_builder' => $query,*/
                'required' => false,
            ])

            /*->add('dateExpiration', DateTimeType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control dateExpiration'],
            ])*/
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control desc'],
                ])
            ->add('publiable', ChoiceType::class, [
                    'choices' => [
                        'No' => 0,
                        'Yes' => 1,
                ],
                'required' => false,
                'attr' => ['class' => 'form-control publiable'],
            ])
            ->add('dureeGarantie', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control dureeGarantie'],
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Disponible en stock' => 0,
                    'Non disponible en stock' => 1,
                    'Vente sur commande' => 2,
                    'Vente suspendue' => 3,
                    'Vente annulée' => 4,
                    'Stock limité' => 5,
                    'En panne' => 6,
                    'Vente régionale' => 7,
                    'Vente interdite' => 8,
                ],
                'attr' => ['class' => 'form-control etat'],
                'required'=> true,
            ])
            ->add('apparenceNeuf', ChoiceType::class, [
                'choices' => [
                    'Neuf' => 1,
                    'Occassion' => 0,
                ],
                'required' => false,
                'attr' => ['class' => 'form-control apparence'],
            ])
            ->add('modeVente', ChoiceType::class, [
                'choices' => array(
                    'VENTE NORMALE' => 0,
                    'VENTE AUX ENCHERES' => 1,
                    'VENTE EN SOLDE' => 2,
                    'VENTE RESTREINTE' => 3,
                ),
                'attr' => ['class' => 'form-control modeVente'],
                'required' => true,
            ])
            ->add('modelDeSerie', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control modelDeSerie']
            ])
            ->add('prixUnitaire', MoneyType::class, [
                'currency' => 'XAF',
                'required' => false,
                'attr' => ['class' => 'form-control prix']
            ])
            ->add('quantite', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control quantite']
                ])

            ->add('unite', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control unite']
            ])
            ->add('remiseProduit', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control remise']
            ])
            ->add('typeOffre', ChoiceType::class, [
                'choices' => [
                    'ARTICLE' => 0,
                    'PRODUIT' => 1,
                    'SERVICE' => 2
                ],
                'attr' => ['class' => 'form-control type'],
                    'required' => true,
                ]

            )
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
            ])
        ;
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Offre',
            'allow_extra_fields' => true,
        ));
    }
}
