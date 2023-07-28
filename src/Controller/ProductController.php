<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\CsvImportType;
use App\Form\ProductImageType;
use App\Form\ProductImportType;
use App\Form\ProductType;
use App\Message\ProductUpdateNotification;
use App\Repository\ProductImageRepository;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    private function buildSearchQuery(string $searchTerms, QueryBuilder $qb): QueryBuilder
    {

        $words = preg_split("/\s/", $searchTerms);
        if(sizeof($words) == 1) {
            $qb->andWhere("p.itemNumber LIKE :searchTermsItemNumber OR p.name LIKE :searchTermsName")
                ->setParameters(new ArrayCollection ([
                    new Parameter("searchTermsItemNumber", $words[0] . "%"),
                    new Parameter("searchTermsName", "%" . $words[0] . "%")
                ]));                
        } else {
            $params = [];
            for ($i = 0; $i < sizeof($words); $i++) {
                $params[] = new Parameter($i, "%" . $words[$i] . "%");
                $qb->andWhere("p.name LIKE ?$i");
            }
            $qb->setParameters(new ArrayCollection($params));
        }

        return $qb;

    }

    #[Route('/data', name: 'app_product_data', methods: ['GET', 'POST'])]
    public function data(Request $request, ProductRepository $productRepository): JsonResponse
    {
        
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = (array) $request->get('order', []);

        $totalItems = $productRepository->count([]);
        $filteredItems = $productRepository->count([]);

        $qb = $productRepository->createQueryBuilder('p')->orderBy($order[0]['column_name'], $order[0]['dir']);
        
        $items = $this->buildSearchQuery($search['value'], $qb)->setFirstResult($start)->setMaxResults($length)->getQuery()->getResult();

        $qb = $productRepository->createQueryBuilder('p')->select('count(p.id)');

        $filteredItems = $this->buildSearchQuery($search['value'], $qb)->getQuery()->getSingleScalarResult();

        $results = [];
        foreach ($items as $item) {
            $results[] = [                
                'id' => $item->getId(),
                'itemNumber' => $item->getItemNumber(),
                'releaseDate' => $item->getReleaseDate(),
                'name' => $item->getName()
            ];
        }
        
        $data = [
            'draw' => $draw,
            'recordsTotal' => $totalItems,
            'recordsFiltered' => $filteredItems,
            'data' => $results
        ];

        return $this->json($data);

    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/import-progress', name: 'app_product_import_progress')]
    public function importProgress(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $batch = $request->get('batch', 0);
        $totalBatches = $request->get('totalBatches', 0);
        $importKey = $request->get('importKey');

        if ($batch <= $totalBatches) {

            $filename = $this->getParameter("app.import_dir") . "/product_import/" . $importKey . "_" . $batch . ".tmp";

            $fh = new SplFileObject($filename, "r");

            $items = [];

            while (!$fh->eof()) {

                $data = $fh->fgetcsv();

                if ($data !== null && sizeof($data) > 1) {

                    $t = new \App\Model\Product();
                    $t->setItemNumber($data[0]);
                    $t->setName($data[1]);
                    $t->setManufacturerCode($data[4]);
                    $t->setTypeCode($data[5]);
                    $t->setCategoryCodes(explode('|', $data[6]));
                    if ($releaseDate = DateTime::createFromFormat('Y-m-d', $data[2])) {
                        $t->setReleaseDate($releaseDate);
                    }

                    $items[] = $t;

                }

            }

            $bus->dispatch(new ProductUpdateNotification($items));

            $fh = null;

            unlink($filename);

        }

        return new JsonResponse(['batch' => $batch, 'totalBatches' => $totalBatches]);

    }
    
    #[Route('/import-confirm', name: 'app_product_import_confirm')]
    public function importConfirm(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        return $this->render('product/import_progress.html.twig', [
            'importKey' => $importKey,
            'totalBatches' => $totalBatches
        ]);

    }
    
    #[Route('/import-cancel', name: 'app_product_import_cancel')]
    public function importCancel(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        for ($i = 0; $i <= $totalBatches; $i++) {
            unlink($this->getParameter("app.import_dir") . "/product_import/" . $importKey . "_" . $i . ".tmp");
        }

        return $this->redirectToRoute('app_product_index');

    }

    #[Route('/import', name: 'app_product_import')]
    public function import(Request $request): Response
    {

        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $importKey = uniqid();
            $currentBatch = 0;

            $skipFirst = $form->get('skipFirst')->getData();
            $importFile = $form->get('importFile')->getData();

            if ($importFile) {                

                $f = $importFile->openFile("r");

                $filesystem = new Filesystem();
                $filesystem->mkdir($this->getParameter("app.import_dir") . "/product_import/");

                $fh = new SplFileObject($this->getParameter("app.import_dir") . "/product_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");

                $totalLines = 0;

                while(!$f->eof()) { 
                    $line = $f->fgetcsv();
                    if ($totalLines == 0 && $skipFirst) {
                        $totalLines++;
                        continue;
                    }
                    $fh->fputcsv($line);                   
                    if ((++$totalLines % 100) == 0) {
                        $currentBatch++;
                        $fh = null;
                        $fh = new SplFileObject($this->getParameter("app.import_dir") . "/product_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");
                    }
                }

                $f = null;
                $fh = null;

            }

            $sampleFilename = $this->getParameter("app.import_dir") . "/product_import/" . $importKey . "_0.tmp";

            return $this->render('product/import_confirm.html.twig', [
                'importKey' => $importKey,
                'totalBatches' => $currentBatch,
                'importSample' => file_get_contents($sampleFilename)
            ]);

        }

        return $this->render('product/import.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/add-image/{id}', name: 'app_product_add_image')]
    public function addImage(Request $request, Product $product, ProductRepository $productRepository, ProductImageRepository $productImageRepository): Response
    {

        $productImage = new ProductImage();
        $form = $this->createForm(ProductImageType::class, $productImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productImageRepository->save($productImage, true);
            $product->addImage($productImage);
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()]);        
        }

        return $this->render('product/add_image.html.twig', [
            'product' => $product,
            'productImage' => $productImage,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
