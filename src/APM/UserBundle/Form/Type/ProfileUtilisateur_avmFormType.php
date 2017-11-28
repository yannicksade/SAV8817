<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfileUtilisateur_avmFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('dateNaissance', DateType::class)
            ->add('pays', CountryType::class, [
                'required' => false,
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => ['Feminin' => 0, 'Masculin' => 1]
            ])
            ->add('profession')
            ->add('telephone')
            ->add('adresse')
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
