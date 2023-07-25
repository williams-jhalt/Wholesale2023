<?php

namespace App\Controller;

use App\Entity\ProductManufacturer;
use App\Form\CsvImportType;
use App\Form\ProductManufacturerType;
use App\Message\ProductManufacturerUpdateNotification;
use App\Repository\ProductManufacturerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product-manufacturer')]
class ProductManufacturerController extends AbstractController
{
    #[Route('/', name: 'app_product_manufacturer_index', methods: ['GET'])]
    public function index(ProductManufacturerRepository $productManufacturerRepository): Response
    {
        return $this->render('product_manufacturer/index.html.twig', [
            'product_manufacturers' => $productManufacturerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_manufacturer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductManufacturerRepository $productManufacturerRepository): Response
    {
        $productManufacturer = new ProductManufacturer();
        $form = $this->createForm(ProductManufacturerType::class, $productManufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productManufacturerRepository->save($productManufacturer, true);

            return $this->redirectToRoute('app_product_manufacturer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_manufacturer/new.html.twig', [
            'product_manufacturer' => $productManufacturer,
            'form' => $form,
        ]);
    }

    #[Route('/import-progress', name: 'app_product_manufacturer_import_progress')]
    public function importProgress(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $batch = $request->get('batch', 0);
        $totalBatches = $request->get('totalBatches', 0);
        $importKey = $request->get('importKey');

        if ($batch <= $totalBatches) {

            $filename = $this->getParameter("app.import_dir") . "/product_manufacturer_import/" . $importKey . "_" . $batch . ".tmp";

            $fh = new \SplFileObject($filename, "r");

            $p = [];

            while (!$fh->eof()) {

                $data = $fh->fgetcsv();

                if ($data !== null && sizeof($data) > 1) {

                    $t = new \App\Model\ProductManufacturer();
                    $t->code = $data[0];
                    $t->name = $data[1];

                    $p[] = $t;

                }

            }

            $bus->dispatch(new ProductManufacturerUpdateNotification($p));

            $fh = null;

            unlink($filename);

        }

        return new JsonResponse(['batch' => $batch, 'totalBatches' => $totalBatches]);

    }

    #[Route('/import', name: 'app_product_manufacturer_import')]
    public function import(Request $request): Response
    {

        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $importKey = uniqid();
            $currentBatch = 0;

            $importFile = $form->get('importFile')->getData();

            if ($importFile) {                

                $f = $importFile->openFile("r");

                $filesystem = new Filesystem();
                $filesystem->mkdir($this->getParameter("app.import_dir") . "/product_manufacturer_import/");

                $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_manufacturer_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");

                $totalLines = 0;

                while(!$f->eof()) { 
                    $fh->fputcsv($f->fgetcsv());                    
                    if ((++$totalLines % 100) == 0) {
                        $currentBatch++;
                        $fh = null;
                        $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_manufacturer_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");
                    }
                }

                $f = null;
                $fh = null;

            }

            return $this->render('product_manufacturer/import_progress.html.twig', [
                'importKey' => $importKey,
                'batch' => 0,
                'totalBatches' => $currentBatch
            ]);

        }

        return $this->render('product_manufacturer/import.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/{id}', name: 'app_product_manufacturer_show', methods: ['GET'])]
    public function show(ProductManufacturer $productManufacturer): Response
    {
        return $this->render('product_manufacturer/show.html.twig', [
            'product_manufacturer' => $productManufacturer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_manufacturer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductManufacturer $productManufacturer, ProductManufacturerRepository $productManufacturerRepository): Response
    {
        $form = $this->createForm(ProductManufacturerType::class, $productManufacturer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productManufacturerRepository->save($productManufacturer, true);

            return $this->redirectToRoute('app_product_manufacturer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_manufacturer/edit.html.twig', [
            'product_manufacturer' => $productManufacturer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_manufacturer_delete', methods: ['POST'])]
    public function delete(Request $request, ProductManufacturer $productManufacturer, ProductManufacturerRepository $productManufacturerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productManufacturer->getId(), $request->request->get('_token'))) {
            $productManufacturerRepository->remove($productManufacturer, true);
        }

        return $this->redirectToRoute('app_product_manufacturer_index', [], Response::HTTP_SEE_OTHER);
    }
}
