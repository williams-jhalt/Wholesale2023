<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\CsvImportType;
use App\Form\ProductCategoryType;
use App\Message\ProductCategoryUpdateNotification;
use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
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

    private function buildSearchQuery(string $searchTerms, QueryBuilder $qb): QueryBuilder
    {

        $words = preg_split("/\s/", $searchTerms);
        if(sizeof($words) == 1) {
            $qb->andWhere("p.code LIKE :searchTermsItemNumber OR p.name LIKE :searchTermsName")
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

    #[Route('/data', name: 'app_product_category_data', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function data(Request $request, ProductCategoryRepository $productCategoryRepository): JsonResponse
    {
        
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = (array) $request->get('order', []);

        $totalItems = $productCategoryRepository->count([]);
        $filteredItems = $productCategoryRepository->count([]);

        $qb = $productCategoryRepository->createQueryBuilder('p')->orderBy($order[0]['column_name'], $order[0]['dir']);
        
        $items = $this->buildSearchQuery($search['value'], $qb)->setFirstResult($start)->setMaxResults($length)->getQuery()->getResult();

        $qb = $productCategoryRepository->createQueryBuilder('p')->select('count(p.id)');

        $filteredItems = $this->buildSearchQuery($search['value'], $qb)->getQuery()->getSingleScalarResult();

        $results = [];
        foreach ($items as $item) {
            $results[] = [                
                'id' => $item->getId(),
                'code' => $item->getCode(),
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
                    $t->setCode($data[0]);
                    $t->setName($data[1]);

                    $p[] = $t;

                }

            }

            $bus->dispatch(new ProductCategoryUpdateNotification($p));

            $fh = null;

            unlink($filename);

        }

        return new JsonResponse(['batch' => $batch, 'totalBatches' => $totalBatches]);

    }
    
    #[Route('/import-confirm', name: 'app_product_category_import_confirm')]
    public function importConfirm(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        return $this->render('product_category/import_progress.html.twig', [
            'importKey' => $importKey,
            'totalBatches' => $totalBatches
        ]);

    }
    
    #[Route('/import-cancel', name: 'app_product_category_import_cancel')]
    public function importCancel(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        for ($i = 0; $i <= $totalBatches; $i++) {
            unlink($this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $i . ".tmp");
        }

        return $this->redirectToRoute('app_product_category_index');

    }

    #[Route('/import', name: 'app_product_category_import')]
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
                $filesystem->mkdir($this->getParameter("app.import_dir") . "/product_category_import/");

                $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");

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
                        $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");
                    }
                }

                $f = null;
                $fh = null;

            }

            $sampleFilename = $this->getParameter("app.import_dir") . "/product_category_import/" . $importKey . "_0.tmp";

            return $this->render('product_category/import_confirm.html.twig', [
                'importKey' => $importKey,
                'totalBatches' => $currentBatch,
                'importSample' => file_get_contents($sampleFilename)
            ]);

        }

        return $this->render('product_category/import.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/{id}', name: 'app_product_category_show', methods: ['GET'], options: ['expose' => true])]
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

            return $this->redirectToRoute('app_product_category_show', ['id' => $productCategory->getId()], Response::HTTP_SEE_OTHER);
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
