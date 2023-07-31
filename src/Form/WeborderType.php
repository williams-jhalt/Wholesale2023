<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Weborder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeborderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderNumber', TextType::class, ['label' => 'Order Number', 'disabled' => true])
            ->add('customer', CustomerAutocompleteField::class)
            ->add('reference1')
            ->add('reference2')
            ->add('reference3')
            ->add('shipToName')
            ->add('shipToAddress')
            ->add('shipToAddress2')
            ->add('shipToAddress3')
            ->add('shipToCity')
            ->add('shipToState')
            ->add('shipToZip')
            ->add('shipToCountry')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Weborder::class,
        ]);
    }
}
