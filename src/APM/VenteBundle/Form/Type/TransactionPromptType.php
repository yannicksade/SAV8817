<?php
namespace APM\VenteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 04/02/2017
 * Time: 07:58
 */
class TransactionPromptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motif', TextType::class, ['required' => true])
            ->add('montant', MoneyType::class, ['required' => true, 'currency' => 'XAF'])
            ->add('utilisateurNonAVM', TextType::class, ['required' => false])
            ->add('quantite', NumberType::class, ['required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transaction_prompt';
    }

}