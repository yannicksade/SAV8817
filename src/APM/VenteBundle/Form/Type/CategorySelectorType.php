<?php

namespace APM\VenteBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySelectorType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addModelTransformer(new ObjectToStringTransformer($this->manager));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Le type selectionné n\'esxiste pas!!'
        ));
    }

    public function getParent()
    {
        return TextType::class;

    }
}
