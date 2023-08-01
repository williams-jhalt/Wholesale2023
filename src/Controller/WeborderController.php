<?php

namespace App\Controller;

use App\Entity\Weborder;
use App\Form\WeborderType;
use App\Repository\WeborderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/weborder')]
class WeborderController extends AbstractController
{
    #[Route('/', name: 'app_weborder_index', methods: ['GET'])]
    public function index(WeborderRepository $weborderRepository): Response
    {
        return $this->render('weborder/index.html.twig', [
            'weborders' => $weborderRepository->findAll(),
        ]);
    }

    private function buildSearchQuery(string $searchTerms, QueryBuilder $qb): QueryBuilder
    {

        $words = preg_split("/\s/", $searchTerms);
        if (sizeof($words) == 1) {
            $qb->andWhere("o.orderNumber LIKE :searchTerms")
                ->orWhere("o.reference1 LIKE :searchTerms")
                ->orWhere("o.reference2 LIKE :searchTerms")
                ->orWhere("o.reference3 LIKE :searchTerms")
                ->orWhere("o.shipToName LIKE :searchTerms")
                ->setParameter("searchTerms", $words[0] . "%");
        } else {
            $params = [];
            for ($i = 0; $i < sizeof($words); $i++) {
                $params[] = new Parameter($i, "%" . $words[$i] . "%");
                $qb->andWhere("p.shipToName LIKE ?$i");
            }
            $qb->setParameters(new ArrayCollection($params));
        }

        return $qb;
    }


    #[Route('/data', name: 'app_weborder_data', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function data(Request $request, WeborderRepository $weborderRepository): JsonResponse
    {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = (array) $request->get('order', []);

        $totalItems = $weborderRepository->count([]);
        $filteredItems = $weborderRepository->count([]);

        $qb = $weborderRepository->createQueryBuilder('o')->orderBy($order[0]['column_name'], $order[0]['dir']);

        $items = $this->buildSearchQuery($search['value'], $qb)->setFirstResult($start)->setMaxResults($length)->getQuery()->getResult();

        $qb = $weborderRepository->createQueryBuilder('o')->select('count(o.id)');

        $filteredItems = $this->buildSearchQuery($search['value'], $qb)->getQuery()->getSingleScalarResult();

        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->getId(),
                'customer' => $item->getCustomer()->getCustomerNumber(),
                'orderNumber' => $item->getOrderNumber(),
                'reference1' => $item->getReference1(),
                'reference2' => $item->getReference2(),
                'reference3' => $item->getReference3(),
                'orderDate' => $item->getOrderDate()
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

    #[Route('/new', name: 'app_weborder_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WeborderRepository $weborderRepository): Response
    {
        $weborder = new Weborder();
        $form = $this->createForm(WeborderType::class, $weborder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weborderRepository->save($weborder, true);

            return $this->redirectToRoute('app_weborder_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('weborder/new.html.twig', [
            'weborder' => $weborder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weborder_show', methods: ['GET'], options: ['expose' => true])]
    public function show(Weborder $weborder): Response
    {
        return $this->render('weborder/show.html.twig', [
            'weborder' => $weborder,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_weborder_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Weborder $weborder, WeborderRepository $weborderRepository): Response
    {
        $form = $this->createForm(WeborderType::class, $weborder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $weborderRepository->save($weborder, true);

            return $this->redirectToRoute('app_weborder_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('weborder/edit.html.twig', [
            'weborder' => $weborder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weborder_delete', methods: ['POST'])]
    public function delete(Request $request, Weborder $weborder, WeborderRepository $weborderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $weborder->getId(), $request->request->get('_token'))) {
            $weborderRepository->remove($weborder, true);
        }

        return $this->redirectToRoute('app_weborder_index', [], Response::HTTP_SEE_OTHER);
    }
}
