<?php

namespace APM\VenteBundle\Form;

use APM\UserBundle\Entity\Utilisateur_avm;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Rabais_offreType extends AbstractType
{

    private $groupes;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Utilisateur_avm $vendeur */
        $vendeur = $builder->getData()->getOffre()->getVendeur();
        $this->groupes = $vendeur->getGroupesProprietaire()->last();

        $builder
            ->add('dateLimite', DateTimeType::class)
            ->add('prixUpdate', MoneyType::class, [
                'grouping' => true,
                'required' => true,
                'currency' => 'XAF'
            ])
            //Selectionner les indivus d'un groupe constiués par le vendeur
            //si un groupe est selectionné, les individus de ce groupe s'affichent
            ->add('quantiteMin', NumberType::class, ['required' => false])
            ->add('groupe', EntityType::class, [
                'class' => 'APMUserBundle:Groupe_relationnel',
                'required' => true
            ])
            ->add('beneficiaireRabais', EntityType::class, [
                'class' => 'APMUserBundle:Utilisateur_avm',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->join('u.individuGroupes', 'ig_id')
                        ->addSelect('ig_id')
                        ->where('ig_id.groupeRelationnel = :grp_id')
                        ->setParameter('grp_id', $this->groupes);
                },

            ])
            ->add('description')
            ->add('restreint', CheckboxType::class, ['required' => false])
            ->add('permanence', CheckboxType::class, ['required' => false])
            ->add('nombreUtilisation', NumberType::class, ['required' => false])
            ->add('valeur', MoneyType::class, [
                'grouping' => true,
                'required' => false])
            ->add('description');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Rabais_offre'
        ));
    }
}
