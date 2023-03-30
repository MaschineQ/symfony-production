<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PackagingMaterial;
use App\Form\PackagingMaterialType;
use App\Repository\NotificationSettingRepository;
use App\Repository\PackagingMaterialRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PackagingMaterialController extends AbstractController
{
    public function __construct(
        private PackagingMaterialRepository $packagingMaterials,
        private TranslatorInterface $translator,
        private NotificationSettingRepository $notificationSettings,
    ) {
    }

    #[Route('/packaging-material', name: 'app_packaging_material')]
    public function index(): Response
    {
        $notificationSettings = $this->notificationSettings->find(1) ?? $this->notificationSettings->createDefault();
        return $this->render('packaging_material/index.html.twig', [
            'packagingMaterials' => $this->packagingMaterials->findAll(),
            'packagingMaterialCritical' => $notificationSettings->getStockCritical(),
            'packagingMaterialLow' => $notificationSettings->getStockLow(),
        ]);
    }

    #[Route('/packaging-material/add', name: 'app_packaging_material_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request): Response
    {
        $packagingMaterial = new PackagingMaterial();
        $form = $this->createForm(PackagingMaterialType::class, $packagingMaterial);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->packagingMaterials->add($packagingMaterial);

            $this->addFlash('success', $this->translator->trans('Packaging material have been added.'));
            return $this->redirectToRoute('app_packaging_material');
        }

        return $this->render('packaging_material/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/packaging-material/{packagingMaterial}/edit', name: 'app_packaging_material_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, PackagingMaterial $packagingMaterial): Response
    {
        $form = $this->createForm(PackagingMaterialType::class, $packagingMaterial);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->packagingMaterials->add($packagingMaterial);

            $this->addFlash('success', $this->translator->trans('Packaging material have been edited.'));
            return $this->redirectToRoute('app_packaging_material');
        }

        return $this->render('packaging_material/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/packaging-material/{packagingMaterial}/delete', name: 'app_packaging_material_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(PackagingMaterial $packagingMaterial, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->packagingMaterials->remove($packagingMaterial);
            $this->addFlash('success', $this->translator->trans('Packaging material have been deleted.'));
        } catch (ConstraintViolationException | ForeignKeyConstraintViolationException | ConstraintDefinitionException $e) {
            $this->addFlash(
                'error',
                $this->translator->trans('Packaging material cannot be deleted. It is used in other records.')
            );
        }

        return $this->redirectToRoute('app_packaging_material');
    }
}
