<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfileUtilisateur_avmFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephone')
            ->add('adresse')
            ->add('profession')
            ->add('email', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom')
            ->add('isAcheteur')
            ->add('isVendeur')
            ->add('isConseillerA1')
            ->add('isGerantBoutique')
            ->add('isTransporteurLivreur')
            ->add('pointsDeFidelite')
            ->add('dateNaissance', TextType::class, array(
                'attr' => ['class' => 'form-control', 'name' => 'dateNaissance'],
                'required' => false,
            ))
            ->add('pays', CountryType::class, [
                'required' => false,
            ])
            ->add('genre', ChoiceType::class, array(
                'expanded' => true,
                'choices' => [
                    'M' => '1',
                    'F' => '0',
                ],
                'choice_label' => function ($val, $key, $index) {
                    if ($val) return 'Masculin'; else return 'Feminin';
                },
                'choice_attr' => function ($val, $key, $index) {
                    return ($val) ? ['data-title' => 'Masculin'] : ['data-title' => 'Feminin'];
                }
                /*'empty_value'=> 1,
                 'empty_data'=> null*/
            ))
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
            ]);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    // For Symfony 2.x

    public function getBlockPrefix()
    {
        return 'apm_utilisateur_avm_profile';
    }
}
