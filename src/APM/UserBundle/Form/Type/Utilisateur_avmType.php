<?php

namespace APM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Utilisateur_avmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('dateEnregistrement', DateTimeType::class)
            ->add('acheteur', CheckboxType::class, ['required' => true])
            ->add('conseillerA1', CheckboxType::class, ['required' => true])
            ->add('conseillerA2', CheckboxType::class, ['required' => false])
            ->add('gerantBoutique', CheckboxType::class, ['required' => false])
            ->add('transporteurLivreur', CheckboxType::class, ['required' => false])
            ->add('vendeur', CheckboxType::class, ['required' => true])
            ->add('etatDuCompte', ChoiceType::class, [
                'choices' => array(
                    'NORMAL' => 0,
                    'BLOQUE' => 1,
                    'SUSPENDU' => 2,
                    'DANGER' => 3
                )])
            ->add('latitudeX')
            ->add('longitudeY')
            ->add('pointsDeFidelite', NumberType::class, ['required' => false])
            ->add('urlImageProfile', UrlType::class, ['required' => false]);

    }

    public function getParent()
    {
        return 'APM\UserBundle\Form\Type\RegistrationType';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\UserBundle\Entity\Utilisateur_avm'
        ));
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    // For Symfony 2.x

    public function getBlockPrefix()
    {
        return 'apm_utilisateur_avm_registration';
    }
}
