<?php

namespace App\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CustomerService
{

    public function __construct(
        private CustomerRepository $customerRepository,
        private EntityManagerInterface $entityManagerInterface,
        private HttpClientInterface $httpClientInterface,
        private string $erpConnectorUrl,
        private string $erpConnectorToken
    ) {
    }

    public function loadCustomersFromErp()
    {

        $start = 0;
        $limit = 100;

        do {

            $response = $this->httpClientInterface->request(
                'GET',
                $this->erpConnectorUrl . "/api/WTC/customer",
                [
                    'query' => [
                        'offset' => $start,
                        'limit' => $limit
                    ],
                    'headers' => [
                        'X-AUTH-TOKEN' => $this->erpConnectorToken
                    ]
                ]
            );

            $customers = json_decode($response->getContent());

            foreach ($customers as $customer) {
                $c = $this->customerRepository->findOneByCustomerNumber($customer->customerNumber);
                if ($c == null) {
                    $c = new Customer();
                }
                $c->setCustomerNumber($customer->customerNumber);
                $c->setCompany($customer->name);
                $c->setAddress1($customer->billToAddress1);
                $c->setAddress2($customer->billToAddress2);
                $c->setAddress3($customer->billToAddress3);
                $c->setCity($customer->billToCity);
                $c->setState($customer->billToState);
                $c->setPostalCode($customer->billToPostalCode);
                $c->setCountry($customer->billToCountry);
                $this->entityManagerInterface->persist($c);
            }

            $this->entityManagerInterface->flush();
            $this->entityManagerInterface->clear();

            $start = $start + $limit;

        } while (sizeof($customers) > 0);


    }
}
