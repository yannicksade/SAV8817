<?php

namespace APM\VenteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoutiqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, ['required' => false])
            ->add('nationalite', CountryType::class, [
                'required' => false,
                'placeholder' => 'choisir un pays>>'
            ])
            ->add('designation', TextType::class)
            ->add('raisonSociale', TextType::class, ['required' => false])
            ->add('statutSocial', ChoiceType::class, array(
                'choices' => [
                    'S.N.C' => 0,
                    'S.C.S' => 1,
                    'S.A.' => 2,
                    'S.A.R.L' => 3,
                    'S.A.S' => 4,
                    'Autres' => 5
                ],
                'required' => false
            ))
            ->add('gerant')
            ->add('proprietaire');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\VenteBundle\Entity\Boutique'
        ));
    }
}
