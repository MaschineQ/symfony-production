<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Production;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductionManager
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getnumberOfPiecesPerUnit(float $productionQuantity, float $quantityPerPiece): float
    {
        return $productionQuantity / $quantityPerPiece;
    }

    public function isRightNumberOfPiecesPerUnit(float $numberOfPiecesPerUnit, float $productionQuantity): bool
    {
        return floor($numberOfPiecesPerUnit) === $numberOfPiecesPerUnit && $productionQuantity !== 0;
    }


    public function getProductIds(Production $production): array
    {
        $items = $production->getItems();
        $productIds = [];

        foreach ($items as $item) {
            if ($item->getProduct()) {
                $productIds[] = $item->getProduct()->getId();
            }
        }

        return $productIds;
    }

    public function hasDuplicateProduct(Production $production): bool
    {
        $productIds = $this->getProductIds($production);

        return count($productIds) !== count(array_unique($productIds));
    }

    /**
     * @throws Exception
     */
    public function addQuantityToProductAndPackagingMaterial(Production $production): void
    {
        $items = $production->getItems();
        $i = 0;
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product && null !== $item->getQuantity()) {
                $numberOfPieces = $this->getnumberOfPiecesPerUnit(
                    $item->getQuantity(),
                    $product->getQuantityPerPiece()
                );

                if ($this->isRightNumberOfPiecesPerUnit($numberOfPieces, $item->getQuantity()) && $item->getProduct()) {
                    foreach ($product->getPackagingMaterial() as $packagingMaterial) {
                        // add quantity to packaging material
                        $packagingMaterial->setQuantity($packagingMaterial->getQuantity() - (int)$numberOfPieces);
                    }

                    // add quantity to product
                    $item->getProduct()->setQuantity($item->getProduct()->getQuantity() + (int)$numberOfPieces);
                } else {
                    throw new Exception($this->translator->trans('Wrong number of pieces per unit'));
                }
            } else {
                throw new Exception($this->translator->trans('Product not found'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function removeQuantityFromProductAndPackagingMaterial(Production $production): void
    {
        $items = $production->getItems();
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product && null !== $item->getQuantity()) {
                $numberOfPieces = $this->getnumberOfPiecesPerUnit(
                    $item->getQuantity(),
                    $product->getQuantityPerPiece()
                );

                if ($this->isRightNumberOfPiecesPerUnit($numberOfPieces, $item->getQuantity()) && $item->getProduct()) {
                    foreach ($product->getPackagingMaterial() as $packagingMaterial) {
                        // remove quantity from packaging material
                        $packagingMaterial->setQuantity($packagingMaterial->getQuantity() + (int)$numberOfPieces);
                    }

                    // remove quantity from product
                    $item->getProduct()->setQuantity($item->getProduct()->getQuantity() - (int)$numberOfPieces);
                } else {
                    throw new Exception($this->translator->trans('Wrong number of pieces per unit'));
                }
            } else {
                throw new Exception($this->translator->trans('Product not found'));
            }
        }
    }
}
