<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private ProductRepository $products,
        private CategoryRepository $categories
    ) {
    }

    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->products->findAll(),
            'categories' => $this->categories->findBy([], [], 1)
        ]);
    }

    #[Route('/product/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->products->add($product, true);
            $this->addFlash('success', $this->translator->trans('Product have been added.'));
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{product}/edit', name: 'app_product_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->products->add($product, true);

            $this->addFlash('success', $this->translator->trans('Your product have been updated.'));

            return $this->redirectToRoute('app_product');
        }

        return $this->render(
            'product/edit.html.twig',
            [
                'form' => $form->createView(),
                'product' => $product
            ]
        );
    }
}
