<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptType;
use App\Manager\ReceiptManager;
use App\Repository\ReceiptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReceiptController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private ReceiptRepository $receipts,
        private ReceiptManager $receiptManager,
    ) {
    }

    #[Route('/receipt', name: 'app_receipt')]
    public function index(): Response
    {
        return $this->render('receipt/index.html.twig', [
            'receipts' => $this->receipts->findAll(),
        ]);
    }

    #[Route('/receipt/add', name: 'app_receipt_add')]
    public function add(Request $request): Response
    {
        $receipt = new Receipt();
        $form = $this->createForm(ReceiptType::class, $receipt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->receiptManager->addQuantityToPackagingMaterial($receipt);

            $this->receipts->add($receipt);
            $this->addFlash('success', $this->translator->trans('Receipt have been added.'));
            return $this->redirectToRoute('app_receipt');
        }

        return $this->render('receipt/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/receipt/{receipt}/delete', name: 'app_receipt_delete')]
    public function delete(Request $request, Receipt $receipt): Response
    {
        $this->receiptManager->removeQuantityFromPackagingMaterial($receipt);

        $this->receipts->remove($receipt, true);
        $this->addFlash('success', $this->translator->trans('Receipt have been deleted.'));

        return $this->redirectToRoute('app_receipt');
    }
}
