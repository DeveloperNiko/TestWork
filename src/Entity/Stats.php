<?php

namespace App\Entity;

use App\Repository\StatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatsRepository::class)]
class Stats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?URL $url = null;

    #[ORM\Column]
    private ?int $visits = 0;

    public function incrementVisits(): void
    {
        ++$this->visits;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?URL
    {
        return $this->url;
    }

    public function setUrl(?URL $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): static
    {
        $this->visits = $visits;

        return $this;
    }
}
