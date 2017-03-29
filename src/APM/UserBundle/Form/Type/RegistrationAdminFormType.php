<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('roles', ChoiceType::class, array(
                    'choices' => [
                        'UtilisateurAVM' => 'ROLE_USERAVM',
                        'TiersUtilisateur' => 'ROLE_USER',
                        'Analyste' => 'ROLE_ANALYSE',
                        'Explorateur' => 'ROLE_EXP',
                        'Auditeur' => 'ROLE_AUDIT',
                        'Comptable' => 'ROLE_COMP',
                        'Gestionnaire' => 'ROLE_ADMIN',
                        'Boutique' => 'ROLE_BOUTIQUE',
                        'Transporteur' => 'ROLE_TRANSPORTEUR',
                        'Conseiller' => 'ROLE_CONSEILLER',
                    ],
                    'required' => true,
                    'multiple' => true,

                )

            );
    }


    public function getParent()
    {
        return 'APM\UserBundle\Form\Type\RegistrationType';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    // For Symfony 2.x

    public function getBlockPrefix()
    {
        return 'apm_admin_registration';
    }
}