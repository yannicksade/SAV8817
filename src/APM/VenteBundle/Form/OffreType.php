<?php

namespace APM\VenteBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OffreType extends AbstractType
{
    private $vendeur_id;
    private $boutique_id;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $offre = $builder->getData();
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
        };

        $builder
            //recupérer uniquement les catégories des boutiques de utilisateur en tant que gérant ou proprietaire
            ->add('designation', TextType::class, ['required' => true])
            ->add('categorie', EntityType::class,
                [
                    'class' => 'APMVenteBundle:Categorie',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')//* pris en compte si boutique connue
                        ->leftJoin('c.boutique', 'btq')
                            ->addSelect('btq')
                            ->where('c.boutique = :btq_id')//* pris en compte si boutique connue
                            ->setParameter('btq_id', $this->boutique_id)//* pris en compte si boutique connue
                            ->orWhere('btq.gerant = :grt_id OR btq.proprietaire = :prt_id')
                            ->setParameter('grt_id', $this->vendeur_id)
                            ->setParameter('prt_id', $this->vendeur_id)
                            ->orderBy('c.designation', 'ASC');
                    }
                ])
            ->add('boutique', EntityType::class, [
                'class' => 'APMVenteBundle:Boutique',
                //Affiche les boutiques propres et les boutique gérées par le vendeur pour l'affectation de l'offre qu'il créee
                'query_builder' => $query,
                'required' => false,
            ])
            ->add('garantie', CheckboxType::class, ['required' => false])
            ->add('dataSheet', UrlType::class, [
                'default_protocol' => 'ftp',
                'required' => false
            ])
            ->add('credit', NumberType::class, ['required' => false])
            ->add('dateExpiration', DateTimeType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('disponibleEnStock', CheckboxType::class, array(
                'required' => false
            ))
            ->add('publiable', CheckboxType::class, array(
                'required' => false
            ))
            ->add('dateFinGarantie', DateTimeType::class, ['required' => false])
            ->add('retourne', CheckboxType::class, ['required' => false])
            ->add('etat', ChoiceType::class, array(
                'choices' => array(
                    'NEUF' => 0,
                    'OCCASION' => 1
                )
            ))
            ->add('modeVente', ChoiceType::class, [
                'choices' => array(
                    'VENTE NORMALE' => 0,
                    'VENTE AUX ENCHERES' => 1,
                    'VENTE EN SOLDE' => 2,
                    'VENTE RESTREINTE' => 3,
                ),
            ])
            ->add('numeroDeSerie', NumberType::class, ['required' => false])
            ->add('prixUnitaire', MoneyType::class, [
                'currency' => 'XAF',
                'required' => false
            ])
            ->add('quantite', NumberType::class, ['required' => false])
            ->add('evaluation', NumberType::class, ['required' => false])
            ->add('reference', TextType::class, ['required' => false])
            ->add('remiseProduit', PercentType::class, [
                'type' => 'integer',
                'scale' => 2,
                'required' => false
            ])
            ->add('typeOffre', ChoiceType::class, [
                'choices' => [
                    'ARTICLE' => 0,
                    'PRODUIT' => 1,
                    'SERVICE' => 2
                ]])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
            ]);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Offre'
        ));
    }
}
