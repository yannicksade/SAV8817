<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Utilisateur_avmFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function getParent()
    {
        return 'APM\UserBundle\Form\Type\RegistrationType';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }


    public function getBlockPrefix()
    {
        return 'apm_utilisateur_avm_registration';
    }
}
