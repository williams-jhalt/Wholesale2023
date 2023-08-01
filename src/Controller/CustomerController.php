<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    private function buildSearchQuery(string $searchTerms, QueryBuilder $qb): QueryBuilder
    {

        $words = preg_split("/\s/", $searchTerms);
        if (sizeof($words) == 1) {
            $qb->andWhere("c.customerNumber LIKE :searchTerms")
                ->orWhere("c.company LIKE :searchTerms")
                ->setParameter("searchTerms", $words[0] . "%");
        } else {
            $params = [];
            for ($i = 0; $i < sizeof($words); $i++) {
                $params[] = new Parameter($i, "%" . $words[$i] . "%");
                $qb->andWhere("c.company LIKE ?$i");
            }
            $qb->setParameters(new ArrayCollection($params));
        }

        return $qb;
    }

    #[Route('/select', name: 'app_customer_select', methods: ['GET'], options: ['expose' => true])]
    public function select(Request $request, CustomerRepository $customerRepository): JsonResponse
    {
        if (empty($q = $request->get('q'))) {
            $customers = $customerRepository->findAll();
        } else {
            $qb = $customerRepository->createQueryBuilder('c');
            $customers = $this->buildSearchQuery($q, $qb)->getQuery()->getResult();
        }

        $data = [];

        foreach ($customers as $customer) {
            $data[] = $customer->getCustomerNumber();
        }

        return $this->json($data);

    }


    #[Route('/data', name: 'app_customer_data', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function data(Request $request, CustomerRepository $customerRepository): JsonResponse
    {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = (array) $request->get('order', []);

        $totalItems = $customerRepository->count([]);
        $filteredItems = $customerRepository->count([]);

        $qb = $customerRepository->createQueryBuilder('c')->orderBy($order[0]['column_name'], $order[0]['dir']);

        $items = $this->buildSearchQuery($search['value'], $qb)->setFirstResult($start)->setMaxResults($length)->getQuery()->getResult();

        $qb = $customerRepository->createQueryBuilder('c')->select('count(c.id)');

        $filteredItems = $this->buildSearchQuery($search['value'], $qb)->getQuery()->getSingleScalarResult();

        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->getId(),
                'customerNumber' => $item->getCustomerNumber(),
                'company' => $item->getCompany()
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

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->save($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'], options: ['expose' => true])]
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->save($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer, true);
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
