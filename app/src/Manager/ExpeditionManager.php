<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Expedition;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExpeditionManager
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function getProductIds(Expedition $expedition): array
    {
        $items = $expedition->getItems();
        $productIds = [];

        foreach ($items as $item) {
            if ($item->getProduct()) {
                $productIds[] = $item->getProduct()->getId();
            }
        }

        return $productIds;
    }

    public function hasDuplicateProduct(Expedition $expedition): bool
    {
        $productIds = $this->getProductIds($expedition);

        return count($productIds) !== count(array_unique($productIds));
    }

    /**
     * @throws Exception
     */
    public function addQuantityToProduct(Expedition $expedition): void
    {
        $items = $expedition->getItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product && null !== $item->getQuantity()) {
                $product->setQuantity($product->getQuantity() + $item->getQuantity());
            } else {
                throw new Exception($this->translator->trans('Wrong number of pieces per unit'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function removeQuantityFromProduct(Expedition $expedition): void
    {
        $items = $expedition->getItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product && null !== $item->getQuantity()) {
                $product->setQuantity($product->getQuantity() - $item->getQuantity());
            } else {
                throw new Exception($this->translator->trans('Wrong number of pieces per unit'));
            }
        }
    }
}
