<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 04/02/2017
 * Time: 22:36
 */

namespace APM\VenteBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersPromptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateurs', EntityType::class, array(
                    'class' => 'APMUserBundle:Utilisateur_avm',
                    'choice_label' => 'username',
                    'multiple' => true,
                    'required' => true
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user_prompt';
    }
}