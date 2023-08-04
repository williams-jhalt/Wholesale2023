<?php

namespace App\Form;

use App\Repository\CustomerRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CustomerToCustomerNumberTransformer implements DataTransformerInterface
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }

    public function transform($customer): mixed
    {
        if ($customer === null) {
            return '';
        }

        return $customer->getCustomerNumber();
    }

    public function reverseTransform($id): mixed
    {
        if (!$id) {
            return null;
        }

        $customer = $this->customerRepository->findOneByCustomerNumber($id);

        if ($customer === null) {
            throw new TransformationFailedException(sprintf("The customer '%s' does not exist", $id));
        }

        return $customer;
    }
}
