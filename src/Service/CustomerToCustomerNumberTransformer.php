<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CustomerToCustomerNumberTransformer implements DataTransformerInterface
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }

    public function transform($customer)
    {
        if ($customer === null) {
            return '';
        }

        return $customer->getCustomerNumber();
    }

    public function reverseTransform($id)
    {
        if (!$id) {
            return;
        }

        $customer = $this->customerRepository->findOneByCustomerNumber($id);

        if ($customer === null) {
            throw new TransformationFailedException(sprintf("The customer '%s' does not exist", $id));
        }

        return $customer;
    }
}
