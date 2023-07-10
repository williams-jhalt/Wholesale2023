<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
