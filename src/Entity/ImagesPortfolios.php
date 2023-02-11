<?php

namespace App\Entity;

use App\Repository\ImagesPortfoliosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesPortfoliosRepository::class)]
class ImagesPortfolios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'imagesPortfolios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Portfolios $projet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getProjet(): ?Portfolios
    {
        return $this->projet;
    }

    public function setProjet(?Portfolios $projet): self
    {
        $this->projet = $projet;

        return $this;
    }
}
