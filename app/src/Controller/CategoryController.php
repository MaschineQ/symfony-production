<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private CategoryRepository  $categories
    ) {
    }

    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->categories->findAll()
        ]);
    }

    #[Route('/category/add', name: 'app_category_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categories->add($category);

            $this->addFlash('success', $this->translator->trans('Category have been added.'));
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{category}/edit', name: 'app_category_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categories->add($category);

            $this->addFlash('success', $this->translator->trans('Your category have been updated.'));

            return $this->redirectToRoute('app_category');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category
            ]
        );
    }

    #[Route('/category/{category}/delete', name: 'app_category_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Category $category): Response
    {
        try {
            $this->categories->remove($category, true);
            $this->addFlash('success', $this->translator->trans('Category have been deleted.'));
        } catch (ConstraintViolationException | ForeignKeyConstraintViolationException | ConstraintDefinitionException $e) {
            $this->addFlash('error', $this->translator->trans('Category have been used in product.'));
        }

        return $this->redirectToRoute('app_category');
    }
}
