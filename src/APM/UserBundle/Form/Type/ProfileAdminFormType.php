<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class ProfileAdminFormType extends AbstractType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
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
