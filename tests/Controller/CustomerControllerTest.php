<?php

namespace App\Test\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CustomerRepository $repository;
    private string $path = '/customer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Customer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Customer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'customer[customerNumber]' => 'Testing',
            'customer[firstName]' => 'Testing',
            'customer[lastName]' => 'Testing',
            'customer[company]' => 'Testing',
            'customer[address1]' => 'Testing',
            'customer[address2]' => 'Testing',
            'customer[address3]' => 'Testing',
            'customer[city]' => 'Testing',
            'customer[state]' => 'Testing',
            'customer[postalCode]' => 'Testing',
            'customer[country]' => 'Testing',
        ]);

        self::assertResponseRedirects('/customer/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Customer();
        $fixture->setCustomerNumber('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setLastName('My Title');
        $fixture->setCompany('My Title');
        $fixture->setAddress1('My Title');
        $fixture->setAddress2('My Title');
        $fixture->setAddress3('My Title');
        $fixture->setCity('My Title');
        $fixture->setState('My Title');
        $fixture->setPostalCode('My Title');
        $fixture->setCountry('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Customer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Customer();
        $fixture->setCustomerNumber('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setLastName('My Title');
        $fixture->setCompany('My Title');
        $fixture->setAddress1('My Title');
        $fixture->setAddress2('My Title');
        $fixture->setAddress3('My Title');
        $fixture->setCity('My Title');
        $fixture->setState('My Title');
        $fixture->setPostalCode('My Title');
        $fixture->setCountry('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'customer[customerNumber]' => 'Something New',
            'customer[firstName]' => 'Something New',
            'customer[lastName]' => 'Something New',
            'customer[company]' => 'Something New',
            'customer[address1]' => 'Something New',
            'customer[address2]' => 'Something New',
            'customer[address3]' => 'Something New',
            'customer[city]' => 'Something New',
            'customer[state]' => 'Something New',
            'customer[postalCode]' => 'Something New',
            'customer[country]' => 'Something New',
        ]);

        self::assertResponseRedirects('/customer/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCustomerNumber());
        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getCompany());
        self::assertSame('Something New', $fixture[0]->getAddress1());
        self::assertSame('Something New', $fixture[0]->getAddress2());
        self::assertSame('Something New', $fixture[0]->getAddress3());
        self::assertSame('Something New', $fixture[0]->getCity());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getPostalCode());
        self::assertSame('Something New', $fixture[0]->getCountry());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Customer();
        $fixture->setCustomerNumber('My Title');
        $fixture->setFirstName('My Title');
        $fixture->setLastName('My Title');
        $fixture->setCompany('My Title');
        $fixture->setAddress1('My Title');
        $fixture->setAddress2('My Title');
        $fixture->setAddress3('My Title');
        $fixture->setCity('My Title');
        $fixture->setState('My Title');
        $fixture->setPostalCode('My Title');
        $fixture->setCountry('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/customer/');
    }
}
