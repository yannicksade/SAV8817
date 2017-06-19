<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfileAdminFormType extends AbstractType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
      $builder
          ->add('telephone')
          ->add('adresse')
          ->add('imageFile', VichImageType::class, [
              'required' => false,
              'allow_delete' => true,
          ]);
  }

    public function getParent() {
    return 'FOS\UserBundle\Form\Type\ProfileFormType';
  }

    public function getName()
  {
      return $this->getBlockPrefix();
  }

  // For Symfony 2.x

    public function getBlockPrefix()
  {
      return 'apm_admin_profile';
  }
}
