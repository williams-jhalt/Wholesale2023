<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\CsvImportType;
use App\Form\ProductTypeType;
use App\Message\ProductTypeUpdateNotification;
use App\Repository\ProductTypeRepository;
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

#[Route('/product-type')]
class ProductTypeController extends AbstractController
{
    #[Route('/', name: 'app_product_type_index', methods: ['GET'])]
    public function index(ProductTypeRepository $productTypeRepository): Response
    {
        return $this->render('product_type/index.html.twig', [
            'product_types' => $productTypeRepository->findAll(),
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

    #[Route('/data', name: 'app_product_type_data', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function data(Request $request, ProductTypeRepository $repo): JsonResponse
    {
        
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = (array) $request->get('order', []);

        $totalItems = $repo->count([]);
        $filteredItems = $repo->count([]);

        $qb = $repo->createQueryBuilder('p')->orderBy($order[0]['column_name'], $order[0]['dir']);
        
        $items = $this->buildSearchQuery($search['value'], $qb)->setFirstResult($start)->setMaxResults($length)->getQuery()->getResult();

        $qb = $repo->createQueryBuilder('p')->select('count(p.id)');

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

    #[Route('/new', name: 'app_product_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $productType = new ProductType();
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_type/new.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/import-progress', name: 'app_product_type_import_progress')]
    public function importProgress(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $batch = $request->get('batch', 0);
        $totalBatches = $request->get('totalBatches', 0);
        $importKey = $request->get('importKey');

        if ($batch <= $totalBatches) {

            $filename = $this->getParameter("app.import_dir") . "/product_type_import/" . $importKey . "_" . $batch . ".tmp";

            $fh = new \SplFileObject($filename, "r");

            $p = [];

            while (!$fh->eof()) {

                $data = $fh->fgetcsv();

                if ($data !== null && sizeof($data) > 1) {

                    $t = new \App\Model\ProductType();
                    $t->setCode($data[0]);
                    $t->setName($data[1]);

                    $p[] = $t;

                }

            }

            $bus->dispatch(new ProductTypeUpdateNotification($p));

            $fh = null;

            unlink($filename);

        }

        return new JsonResponse(['batch' => $batch, 'totalBatches' => $totalBatches]);

    }
    
    #[Route('/import-confirm', name: 'app_product_type_import_confirm')]
    public function importConfirm(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        return $this->render('product_type/import_progress.html.twig', [
            'importKey' => $importKey,
            'totalBatches' => $totalBatches
        ]);

    }
    
    #[Route('/import-cancel', name: 'app_product_type_import_cancel')]
    public function importCancel(Request $request): Response
    {

        $importKey = $request->get('importKey');
        $totalBatches = $request->get('totalBatches');

        for ($i = 0; $i <= $totalBatches; $i++) {
            unlink($this->getParameter("app.import_dir") . "/product_type_import/" . $importKey . "_" . $i . ".tmp");
        }

        return $this->redirectToRoute('app_product_type_index');

    }

    #[Route('/import', name: 'app_product_type_import')]
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
                $filesystem->mkdir($this->getParameter("app.import_dir") . "/product_type_import/");

                $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_type_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");

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
                        $fh = new \SplFileObject($this->getParameter("app.import_dir") . "/product_type_import/" . $importKey . "_" . $currentBatch . ".tmp", "w");
                    }
                }

                $f = null;
                $fh = null;

            }

            $sampleFilename = $this->getParameter("app.import_dir") . "/product_type_import/" . $importKey . "_0.tmp";

            return $this->render('product_type/import_confirm.html.twig', [
                'importKey' => $importKey,
                'totalBatches' => $currentBatch,
                'importSample' => file_get_contents($sampleFilename)
            ]);

        }

        return $this->render('product_type/import.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/{id}', name: 'app_product_type_show', methods: ['GET'], options: ['expose' => true])]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);

            return $this->redirectToRoute('app_product_type_show', ['id' => $productType->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_type/edit.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_delete', methods: ['POST'])]
    public function delete(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productType->getId(), $request->request->get('_token'))) {
            $productTypeRepository->remove($productType, true);
        }

        return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
