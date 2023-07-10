<?php

namespace App\Controller;

use App\Entity\ProductManufacturer;
use App\Form\ProductManufacturerType;
use App\Repository\ProductManufacturerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product/manufacturer')]
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

        return $this->renderForm('product_manufacturer/new.html.twig', [
            'product_manufacturer' => $productManufacturer,
            'form' => $form,
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

        return $this->renderForm('product_manufacturer/edit.html.twig', [
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
