<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReceiptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiptRepository::class)]
class Receipt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $receiptDate;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'receipt', targetEntity: ReceiptItem::class, cascade: [
        'persist',
        'remove'
    ], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->receiptDate = new \DateTime();
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReceiptDate(): \DateTimeInterface
    {
        return $this->receiptDate;
    }

    public function setReceiptDate(\DateTimeInterface $receiptDate): self
    {
        $this->receiptDate = $receiptDate;

        return $this;
    }


    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, ReceiptItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ReceiptItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setReceipt($this);
        }

        return $this;
    }

    public function removeItem(ReceiptItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getReceipt() === $this) {
                $item->setReceipt(null);
            }
        }

        return $this;
    }
}
