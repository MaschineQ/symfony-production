<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Production;
use App\Form\ProductionType;
use App\Manager\ProductionManager;
use App\Repository\ProductionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductionController extends AbstractController
{
    public function __construct(
        private TranslatorInterface  $translator,
        private ProductionRepository $productions,
        private ProductionManager    $productionManager,
    ) {
    }

    #[Route('/production', name: 'app_production')]
    public function index(): Response
    {
        return $this->render('production/index.html.twig', [
            'productions' => $this->productions->findAll(),
        ]);
    }

    #[Route('/production/add', name: 'app_production_add')]
    public function add(
        Request $request,
    ): Response {
        $production = new Production();

        $form = $this->createForm(ProductionType::class, $production);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productionManager->addQuantityToProductAndPackagingMaterial($production);

            $this->productions->add($production);
            $this->addFlash('success', $this->translator->trans('Production have been added.'));


            return $this->redirectToRoute('app_production');
        }

        return $this->render('production/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/production/{production}/delete')]
    public function delete(Production $production): Response
    {
        $this->productionManager->removeQuantityFromProductAndPackagingMaterial($production);

        $this->productions->remove($production, true);
        $this->addFlash('success', $this->translator->trans('Production have been deleted.'));

        return $this->redirectToRoute('app_production');
    }
}
