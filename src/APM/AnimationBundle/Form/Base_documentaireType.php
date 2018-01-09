<?php

namespace APM\AnimationBundle\Form;

use APM\AnimationBundle\Entity\Base_documentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class Base_documentaireType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objet', TextType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('imagefile', FileType::class, [
                'required' => false,
            ])
            ->add('animationfile', FileType::class, [
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APM\AnimationBundle\Entity\Base_documentaire'
        ));
    }
}
