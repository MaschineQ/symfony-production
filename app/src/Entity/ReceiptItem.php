<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ReceiptItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PackagingMaterial $packagingMaterial = null;

    #[ORM\ManyToOne(inversedBy: 'receipt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Receipt $receipt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPackagingMaterial(): ?PackagingMaterial
    {
        return $this->packagingMaterial;
    }

    public function setPackagingMaterial(?PackagingMaterial $packagingMaterial): self
    {
        $this->packagingMaterial = $packagingMaterial;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }
}
