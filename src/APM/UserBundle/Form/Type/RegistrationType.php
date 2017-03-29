<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
      $builder
          ->add('code')
          ->add('nom', TextType::class)
          ->add('prenom', TextType::class)
          ->add('dateNaissance', DateType::class)
          ->add('pays', CountryType::class, [
              'required' => false,
              'placeholder' => 'choisir un pays'
          ])
          ->add('genre', ChoiceType::class, [
              'choices' => ['Feminin' => 0, 'Masculin' => 1]
          ])
          ->add('telephone', NumberType::class);
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
