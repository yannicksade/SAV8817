<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 04/02/2017
 * Time: 22:36
 */

namespace APM\CoreBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersRolePromptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('utilisateurs', EntityType::class, array('class' => 'APMUserBundle:Utilisateur', 'choice_label' => 'username', 'multiple' => true, 'required' => true))->add('role', ChoiceType::class, array('choices' => ['DOS' => 'ROLE_NO_ACCESS', 'UtilisateurAVM' => 'ROLE_USERAVM', 'TiersUtilisateur' => 'ROLE_USER', 'Analyste' => 'ROLE_ANALYSE', 'Explorateur' => 'ROLE_EXP', 'Auditeur' => 'ROLE_AUDIT', 'Comptable' => 'ROLE_COMP', 'Administrateur' => 'ROLE_ADMIN', 'Boutique' => 'ROLE_BOUTIQUE', 'Transporteur' => 'ROLE_TRANSPORTEUR', 'Conseiller' => 'ROLE_CONSEILLER',], 'required' => false, 'placeholder' => 'mode Affichage...'

            ))->add('ajouterRole', CheckboxType::class, ['required' => false])->add('supprimerRole', CheckboxType::class, ['required' => false]);

    }

    /**
     * {@inheritdoc}
     * retourne le nom du formulaire
     */
    public function getBlockPrefix()
    {
        return 'users_role_prompt';
    }
}