<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 22/04/2017
 * Time: 12:29
 */

namespace APM\CoreBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filter', TextType::class, ['label' => 'Filtre']);
    }

    /**
     * {@inheritdoc}
     * retourne le nom du formulaire
     */
    public function getBlockPrefix()
    {
        return 'filter_form';
    }

}