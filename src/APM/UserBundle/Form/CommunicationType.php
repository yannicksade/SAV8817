<?php

namespace APM\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommunicationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('reference')
            ->add('objet')
            ->add('dateDeVigueur', DateTimeType::class)
            ->add('dateFin', DateTimeType::class, ['required' => false])
            ->add('etat', ChoiceType::class, [
                'required' => true,
                'choices' =>
                    array(
                        'ATTENTE VALIDATION' => 0,
                        'VALIDE' => 1,
                        'ATTENTE PAIEMENT' => 2,
                        'PRET' => 3,
                        'EN COURS' => 4,
                        'TERMINE' => 5,
                        'ANNULEE' => 6,
                    )])
            ->add('type', ChoiceType::class, array(
                'required' => true,
                'choices' => array(
                    'MESSAGE' => 0,
                    'PUB' => 1,
                    'PROMO' => 2,
                    'CIRCULAIRE' => 3,
                    'DEMANDE_RABAIS' => 4,
                    'APPEL_OFFRE' => 5
                )))
            ->add('valide', CheckboxType::class, ['required' => false])
            ->add('contenu', TextareaType::class)
            ->add('recepteur', EntityType::class, [
                'class' => 'APMUserBundle:Utilisateur_avm',
                /*'query_builder' =>function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->join('u.individuGroupes', 'ig_id')
                        ->addSelect('ig_id')
                        ->where('ig_id.groupeRelationnel = :grp_id')
                        ->setParameter('grp_id', $this->groupes);
                },*/
                ///Ne peut communiquer qu'avec les membre d'un ses groupes créer ou d'un groupe auquelle il appartient


            ])
            ->add('documents', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'class' => 'APMAnimationBundle:Base_documentaire',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\UserBundle\Entity\Communication'
        ));
    }
}
