<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Receipt;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReceiptManager
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function getPackagingMaterialIds(Receipt $receipt): array
    {
        $items = $receipt->getItems();
        $packagingMaterialIds = [];

        foreach ($items as $item) {
            if ($item->getPackagingMaterial()) {
                $packagingMaterialIds[] = $item->getPackagingMaterial()->getId();
            }
        }

        return $packagingMaterialIds;
    }

    public function hasDuplicatePackagingMaterial(Receipt $receipt): bool
    {
        $packagingMaterialIds = $this->getPackagingMaterialIds($receipt);

        return count($packagingMaterialIds) !== count(array_unique($packagingMaterialIds));
    }

    /**
     * @throws Exception
     */
    public function addQuantityToPackagingMaterial(Receipt $receipt): void
    {
        $items = $receipt->getItems();
        foreach ($items as $item) {
            $packagingMaterial = $item->getPackagingMaterial();
            if ($packagingMaterial) {
                $packagingMaterial->setQuantity($packagingMaterial->getQuantity() + $item->getQuantity());
            } else {
                throw new Exception($this->translator->trans('Packaging Material not found'));
            }
        }
    }


    /**
     * @throws Exception
     */
    public function removeQuantityFromPackagingMaterial(Receipt $receipt): void
    {
        $items = $receipt->getItems();
        foreach ($items as $item) {
            $packagingMaterial = $item->getPackagingMaterial();
            if ($packagingMaterial) {
                $packagingMaterial->setQuantity($packagingMaterial->getQuantity() + $item->getQuantity());
            } else {
                throw new Exception($this->translator->trans('Packaging Material not found'));
            }
        }
    }
}
