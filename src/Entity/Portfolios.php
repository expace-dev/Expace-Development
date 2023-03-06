<?php

namespace App\Entity;

use App\Repository\PortfoliosRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortfoliosRepository::class)]
class Portfolios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: ImagesPortfolios::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $imagesPortfolios;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'portfolios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projets $projet = null;

    public function __construct()
    {
        $this->imagesPortfolios = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return Collection<int, ImagesPortfolios>
     */
    public function getImagesPortfolios(): Collection
    {
        return $this->imagesPortfolios;
    }

    public function addImagesPortfolio(ImagesPortfolios $imagesPortfolio): self
    {
        if (!$this->imagesPortfolios->contains($imagesPortfolio)) {
            $this->imagesPortfolios->add($imagesPortfolio);
            $imagesPortfolio->setProjet($this);
        }

        return $this;
    }

    public function removeImagesPortfolio(ImagesPortfolios $imagesPortfolio): self
    {
        if ($this->imagesPortfolios->removeElement($imagesPortfolio)) {
            // set the owning side to null (unless already changed)
            if ($imagesPortfolio->getProjet() === $this) {
                $imagesPortfolio->setProjet(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProjet(): ?Projets
    {
        return $this->projet;
    }

    public function setProjet(?Projets $projet): self
    {
        $this->projet = $projet;

        return $this;
    }
}
