<?php

namespace App\Test\Controller;

use App\Entity\ProductManufacturer;
use App\Repository\ProductManufacturerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductManufacturerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProductManufacturerRepository $repository;
    private string $path = '/product-manufacturer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(ProductManufacturer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product_manufacturer[code]' => 'Testing',
            'product_manufacturer[name]' => 'Testing',
        ]);

        self::assertResponseRedirects('/product-manufacturer/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        
        $fixture = new ProductManufacturer();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        
        $fixture = new ProductManufacturer();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product_manufacturer[code]' => 'Something New',
            'product_manufacturer[name]' => 'Something New',
        ]);

        self::assertResponseRedirects('/product-manufacturer/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getName());
    }

    public function testRemove(): void
    {
        

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new ProductManufacturer();
        $fixture->setCode('My Title');
        $fixture->setName('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        $this->client->submitForm('deleteButton');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/product-manufacturer/');
    }
}
