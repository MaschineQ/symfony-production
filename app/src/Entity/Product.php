<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Receipt::class)]
    private Collection $receipt;

    #[ORM\Column(length: 3)]
    private string $packagingType;

    #[ORM\Column]
    private float $quantityPerPiece;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductionItem::class)]
    private Collection $items;

    #[ORM\ManyToMany(targetEntity: PackagingMaterial::class, inversedBy: 'products')]
    #[ORM\JoinColumn(onDelete: 'restrict')]
    #[ORM\InverseJoinColumn(onDelete: 'restrict')]
    private Collection $packagingMaterial;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ExpeditionItem::class, orphanRemoval: true)]
    private Collection $expeditionItems;

    public function __construct()
    {
        $this->receipt = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->packagingMaterial = new ArrayCollection();
        $this->expeditionItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Receipt>
     */
    public function getReceipt(): Collection
    {
        return $this->receipt;
    }

    public function getPackagingType(): string
    {
        return $this->packagingType;
    }

    public function setPackagingType(string $packagingType): self
    {
        $this->packagingType = $packagingType;

        return $this;
    }

    public function getQuantityPerPiece(): float
    {
        return $this->quantityPerPiece;
    }

    public function setQuantityPerPiece(float $quantityPerPiece): self
    {
        $this->quantityPerPiece = $quantityPerPiece;

        return $this;
    }

    /**
     * @return Collection<int, ProductionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ProductionItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setProduct($this);
        }

        return $this;
    }

    public function removeItem(ProductionItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getProduct() === $this) {
                $item->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PackagingMaterial>
     */
    public function getPackagingMaterial(): Collection
    {
        return $this->packagingMaterial;
    }

    public function addPackagingMaterial(PackagingMaterial $packagingMaterial): self
    {
        if (!$this->packagingMaterial->contains($packagingMaterial)) {
            $this->packagingMaterial->add($packagingMaterial);
        }

        return $this;
    }

    public function removePackagingMaterial(PackagingMaterial $packagingMaterial): self
    {
        $this->packagingMaterial->removeElement($packagingMaterial);

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, ExpeditionItem>
     */
    public function getExpeditionItems(): Collection
    {
        return $this->expeditionItems;
    }

    public function addExpeditionItem(ExpeditionItem $expeditionItem): self
    {
        if (!$this->expeditionItems->contains($expeditionItem)) {
            $this->expeditionItems->add($expeditionItem);
            $expeditionItem->setProduct($this);
        }

        return $this;
    }

    public function removeExpeditionItem(ExpeditionItem $expeditionItem): self
    {
        if ($this->expeditionItems->removeElement($expeditionItem)) {
            // set the owning side to null (unless already changed)
            if ($expeditionItem->getProduct() === $this) {
                $expeditionItem->setProduct(null);
            }
        }

        return $this;
    }
}
