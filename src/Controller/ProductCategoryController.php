<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\CsvImportType;
use App\Form\ProductCategoryType;
use App\Message\ProductCategoryUpdateNotification;
use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product-category')]
class ProductCategoryController extends AbstractController
{
    #[Route('/', name: 'app_product_category_index', methods: ['GET'])]
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        return $this->render('product_category/index.html.twig', [
            'product_categories' => $productCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductCategoryRepository $productCategoryRepository): Response
    {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productCategoryRepository->save($productCategory, true);

            return $this->redirectToRoute('app_product_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/import-progress', name: 'app_product_category_import_progress')]
    public function importProgress(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $batch = $request->get('batch', 0);
        $totalBatches = $request->get('totalBatches', 0);
        $importKey = $request->get('importKey');

        if ($batch <= $totalBatches) {

            $filename = $this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $batch . ".tmp";

            $fh = new \SplFileObject($filename, "r");

            $p = [];

            while (!$fh->eof()) {

                $data = $fh->fgetcsv();

                if ($data !== null && sizeof($data) > 1) {

                    $t = new \App\Model\ProductCategory();
                    $t->code = $data[0];
                    $t->name = $data[1];

                    $p[] = $t;

                }

            }

            $bus->dispatch(new ProductCategoryUpdateNotification($p));

            $fh = null;

            unlink($filename);

        }

        return new JsonResponse(['batch' => $batch, 'totalBatches' => $totalBatches]);

    }

    #[Route('/import', name: 'app_product_category_import')]
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
                $filesystem->mkdir($this->getParameter("app.import_dir") . "/product_category_import/");

                $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");

                $totalLines = 0;

                while(!$f->eof()) { 
                    $fh->fputcsv($f->fgetcsv());                    
                    if ((++$totalLines % 100) == 0) {
                        $currentBatch++;
                        $fh = null;
                        $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");
                    }
                }

                $f = null;
                $fh = null;

            }

            return $this->render('product_category/import_progress.html.twig', [
                'importKey' => $importKey,
                'batch' => 0,
                'totalBatches' => $currentBatch
            ]);

        }

        return $this->render('product_category/import.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/{id}', name: 'app_product_category_show', methods: ['GET'])]
    public function show(ProductCategory $productCategory): Response
    {
        return $this->render('product_category/show.html.twig', [
            'product_category' => $productCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductCategory $productCategory, ProductCategoryRepository $productCategoryRepository): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productCategoryRepository->save($productCategory, true);

            return $this->redirectToRoute('app_product_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_category_delete', methods: ['POST'])]
    public function delete(Request $request, ProductCategory $productCategory, ProductCategoryRepository $productCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productCategory->getId(), $request->request->get('_token'))) {
            $productCategoryRepository->remove($productCategory, true);
        }

        return $this->redirectToRoute('app_product_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
