<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Expedition;
use App\Form\ExpeditionType;
use App\Manager\ExpeditionManager;
use App\Repository\ExpeditionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpeditionController extends AbstractController
{
    public function __construct(
        private TranslatorInterface  $translator,
        private ExpeditionRepository $expeditions,
        private ExpeditionManager    $expeditionManager,
    ) {
    }

    #[Route('/expedition', name: 'app_expedition')]
    public function index(): Response
    {
        return $this->render('expedition/index.html.twig', [
            'expeditions' => $this->expeditions->findAll(),
        ]);
    }

    #[Route('/expedition/add', name: 'app_expedition_add')]
    public function add(
        Request $request,
    ): Response {
        $expedition = new Expedition();

        $form = $this->createForm(ExpeditionType::class, $expedition);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->expeditionManager->removeQuantityFromProduct($expedition);

            $this->expeditions->save($expedition);
            $this->addFlash('success', $this->translator->trans('Expeditions have been added.'));


            return $this->redirectToRoute('app_expedition');
        }

        return $this->render('expedition/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/expedition/{expedition}/delete')]
    public function delete(Expedition $expedition): Response
    {
        $this->expeditionManager->addQuantityToProduct($expedition);

        $this->expeditions->remove($expedition, true);
        $this->addFlash('success', $this->translator->trans('Expedition have been deleted.'));

        return $this->redirectToRoute('app_expedition');
    }
}
