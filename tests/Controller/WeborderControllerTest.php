<?php

namespace App\Test\Controller;

use App\Entity\Weborder;
use App\Repository\WeborderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeborderControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private WeborderRepository $repository;
    private string $path = '/weborder/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Weborder::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Weborder index');

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
            'weborder[orderNumber]' => 'Testing',
            'weborder[reference1]' => 'Testing',
            'weborder[reference2]' => 'Testing',
            'weborder[reference3]' => 'Testing',
            'weborder[shipToName]' => 'Testing',
            'weborder[shipToAddress]' => 'Testing',
            'weborder[shipToAddress2]' => 'Testing',
            'weborder[shipToAddress3]' => 'Testing',
            'weborder[shipToCity]' => 'Testing',
            'weborder[shipToState]' => 'Testing',
            'weborder[shipToZip]' => 'Testing',
            'weborder[shipToCountry]' => 'Testing',
        ]);

        self::assertResponseRedirects('/weborder/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Weborder();
        $fixture->setOrderNumber('My Title');
        $fixture->setReference1('My Title');
        $fixture->setReference2('My Title');
        $fixture->setReference3('My Title');
        $fixture->setShipToName('My Title');
        $fixture->setShipToAddress('My Title');
        $fixture->setShipToAddress2('My Title');
        $fixture->setShipToAddress3('My Title');
        $fixture->setShipToCity('My Title');
        $fixture->setShipToState('My Title');
        $fixture->setShipToZip('My Title');
        $fixture->setShipToCountry('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Weborder');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Weborder();
        $fixture->setOrderNumber('My Title');
        $fixture->setReference1('My Title');
        $fixture->setReference2('My Title');
        $fixture->setReference3('My Title');
        $fixture->setShipToName('My Title');
        $fixture->setShipToAddress('My Title');
        $fixture->setShipToAddress2('My Title');
        $fixture->setShipToAddress3('My Title');
        $fixture->setShipToCity('My Title');
        $fixture->setShipToState('My Title');
        $fixture->setShipToZip('My Title');
        $fixture->setShipToCountry('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'weborder[orderNumber]' => 'Something New',
            'weborder[reference1]' => 'Something New',
            'weborder[reference2]' => 'Something New',
            'weborder[reference3]' => 'Something New',
            'weborder[shipToName]' => 'Something New',
            'weborder[shipToAddress]' => 'Something New',
            'weborder[shipToAddress2]' => 'Something New',
            'weborder[shipToAddress3]' => 'Something New',
            'weborder[shipToCity]' => 'Something New',
            'weborder[shipToState]' => 'Something New',
            'weborder[shipToZip]' => 'Something New',
            'weborder[shipToCountry]' => 'Something New',
        ]);

        self::assertResponseRedirects('/weborder/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getOrderNumber());
        self::assertSame('Something New', $fixture[0]->getReference1());
        self::assertSame('Something New', $fixture[0]->getReference2());
        self::assertSame('Something New', $fixture[0]->getReference3());
        self::assertSame('Something New', $fixture[0]->getShipToName());
        self::assertSame('Something New', $fixture[0]->getShipToAddress());
        self::assertSame('Something New', $fixture[0]->getShipToAddress2());
        self::assertSame('Something New', $fixture[0]->getShipToAddress3());
        self::assertSame('Something New', $fixture[0]->getShipToCity());
        self::assertSame('Something New', $fixture[0]->getShipToState());
        self::assertSame('Something New', $fixture[0]->getShipToZip());
        self::assertSame('Something New', $fixture[0]->getShipToCountry());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Weborder();
        $fixture->setOrderNumber('My Title');
        $fixture->setReference1('My Title');
        $fixture->setReference2('My Title');
        $fixture->setReference3('My Title');
        $fixture->setShipToName('My Title');
        $fixture->setShipToAddress('My Title');
        $fixture->setShipToAddress2('My Title');
        $fixture->setShipToAddress3('My Title');
        $fixture->setShipToCity('My Title');
        $fixture->setShipToState('My Title');
        $fixture->setShipToZip('My Title');
        $fixture->setShipToCountry('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/weborder/');
    }
}
