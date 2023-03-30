<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExpeditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpeditionRepository::class)]
class Expedition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private \DateTimeImmutable $expeditionDate;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'expedition', targetEntity: ExpeditionItem::class, cascade: [
        'persist',
        'remove'
    ], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->expeditionDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpeditionDate(): ?\DateTimeImmutable
    {
        return $this->expeditionDate;
    }

    public function setExpeditionDate(\DateTimeImmutable $expeditionDate): self
    {
        $this->expeditionDate = $expeditionDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, ExpeditionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ExpeditionItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setExpedition($this);
        }

        return $this;
    }

    public function removeItem(ExpeditionItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getExpedition() === $this) {
                $item->setExpedition(null);
            }
        }

        return $this;
    }
}
