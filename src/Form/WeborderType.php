<?php

namespace App\Form;

use App\Entity\Weborder;
use App\Service\CustomerToCustomerNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeborderType extends AbstractType
{

    public function __construct(
        private CustomerToCustomerNumberTransformer $transformer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderNumber', TextType::class, ['label' => 'Order Number', 'disabled' => true])
            ->add('customer', TextType::class, ['attr' => ['class' => 'basicAutoComplete']])
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

        $builder->get('customer')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Weborder::class,
        ]);
    }
}
