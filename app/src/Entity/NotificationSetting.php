<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NotificationSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationSettingRepository::class)]
class NotificationSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column]
    private int $stockCritical;

    #[ORM\Column]
    private int $stockLow;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStockCritical(): int
    {
        return $this->stockCritical;
    }

    public function setStockCritical(int $stockCritical): self
    {
        $this->stockCritical = $stockCritical;

        return $this;
    }

    public function getStockLow(): int
    {
        return $this->stockLow;
    }

    public function setStockLow(int $stockLow): self
    {
        $this->stockLow = $stockLow;

        return $this;
    }
}
