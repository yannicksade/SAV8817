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
            ->add('email', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom')
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('attr' => array('class' => 'form-control')),
                'required' => true,
            ))
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
                     return ($val)?['data-title' =>'Masculin'] : ['data-title' =>'Feminin'];
                }
                /*'empty_value'=> 1,
                 'empty_data'=> null*/
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
        return 'apm_utilisateur_registration';
    }
}
