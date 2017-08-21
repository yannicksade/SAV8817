<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
           ->add('plainPassword', RepeatedType::class, array(
               'type' => PasswordType::class,
               'invalid_message' => 'fos_user.password.mismatch',
               'options' => array('attr' => array('class' => 'form-control')),
               'required' => true,
           ))
            ->add('dateNaissance', TextType::class , array(
                'attr' => ['class'=>'form-control']
            ))

            ->add('pays', CountryType::class, [
                'required' => false,
            ])

            ->add('genre', ChoiceType::class, array(
                'expanded' => true,
                'choices' => [
                    'male' => '1',
                    'female' =>'0',
                ],
                'required' => true
            ))
            ->add('telephone', NumberType::class)
            ->add('adresse', TextType::class)
            ->add('profession', TextType::class)
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
            ]);

    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    // For Symfony 2.x

    public function getBlockPrefix()
    {
        return 'app_utilisateur_registration';
    }
}
