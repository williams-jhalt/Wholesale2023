<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ProductImportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('importFile', FileType::class, [
            'label' => "Product File (CSV)",
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '20M',
                    'mimeTypes' => [
                        'text/csv'
                    ],
                    'mimeTypesMessage' => 'Plesae upload a CSV file'
                ])
            ]
        ])->add("skipFirst", CheckboxType::class, [
            'label' => "Skip First Line"
        ]);
    }

}