<?php

namespace App\Entity;

use App\Repository\FacturesRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturesRepository::class)]
class Factures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $client = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $services = [];

    #[ORM\Column(length: 255)]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projets $projet = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToOne(mappedBy: 'facture', cascade: ['persist', 'remove'])]
    private ?Paiements $paiements = null;

    public function __construct()
    {
        $this->statut = 'en_attente';
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Users
    {
        return $this->client;
    }

    public function setClient(?Users $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(array $services): self
    {
        $this->services = $services;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPaiements(): ?Paiements
    {
        return $this->paiements;
    }

    public function setPaiements(Paiements $paiements): self
    {
        // set the owning side of the relation if necessary
        if ($paiements->getFacture() !== $this) {
            $paiements->setFacture($this);
        }

        $this->paiements = $paiements;

        return $this;
    }
}
