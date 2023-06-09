<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExpeditionItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpeditionItemRepository::class)]
class ExpeditionItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Expedition $expedition = null;

    #[ORM\ManyToOne(inversedBy: 'expeditionItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product;

    #[ORM\Column]
    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getExpedition(): ?Expedition
    {
        return $this->expedition;
    }

    public function setExpedition(?Expedition $expedition): self
    {
        $this->expedition = $expedition;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
