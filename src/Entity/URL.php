<?php

namespace App\Entity;

use App\Repository\URLRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: URLRepository::class)]
class URL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $original_url = null;

    #[ORM\Column(length: 255)]
    private ?string $short_url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expiry_date = null;

    #[ORM\OneToOne(targetEntity: Stats::class, mappedBy: "url", cascade: ['persist'])]
    private ?Stats $stats = null;

    public function __construct()
    {
        $this->stats = new Stats();

    }

    public function incrementVisits(): void
    {
        if ($this->stats) {
            $this->stats->incrementVisits();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->original_url;
    }

    public function setOriginalUrl(string $original_url): static
    {
        $this->original_url = $original_url;

        return $this;
    }

    public function getShortUrl(): ?string
    {
        return $this->short_url;
    }

    public function setShortUrl(string $short_url): static
    {
        $this->short_url = $short_url;

        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiry_date;
    }

    public function setExpiryDate(\DateTimeInterface $expiry_date): static
    {
        $this->expiry_date = $expiry_date;

        return $this;
    }

    public function setStats(Stats $stats): void
    {
        $this->stats = $stats;
    }

    public function getStats(): ?Stats
    {
        return $this->stats;
    }
}
