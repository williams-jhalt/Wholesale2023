<?php

namespace App\Test\Controller;

use App\Entity\ProductType;
use App\Repository\ProductTypeRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTypeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProductTypeRepository $repository;
    private string $path = '/product-type/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(ProductType::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product_type[code]' => 'Testing',
            'product_type[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/product-type/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        
        $fixture = new ProductType();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        
        $fixture = new ProductType();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product_type[code]' => 'Something New',
            'product_type[name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/product-type/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getName());
    }

    public function testRemove(): void
    {
        

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new ProductType();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $this->client->submitForm('deleteButton');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/product-type/');
    }
}
