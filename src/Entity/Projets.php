<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'projets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $client = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $besoinClient = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $propositionCommercial = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cahierCharge = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Devis::class, orphanRemoval: true)]
    private Collection $devis;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Factures::class, orphanRemoval: true)]
    private Collection $factures;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug() {
        if(empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->titre);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

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

    public function getClient(): ?Users
    {
        return $this->client;
    }

    public function setClient(?Users $client): self
    {
        $this->client = $client;

        return $this;
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

    public function getBesoinClient(): ?string
    {
        return $this->besoinClient;
    }

    public function setBesoinClient(?string $besoinClient): self
    {
        $this->besoinClient = $besoinClient;

        return $this;
    }

    public function getPropositionCommercial(): ?string
    {
        return $this->propositionCommercial;
    }

    public function setPropositionCommercial(?string $propositionCommercial): self
    {
        $this->propositionCommercial = $propositionCommercial;

        return $this;
    }

    public function getCahierCharge(): ?string
    {
        return $this->cahierCharge;
    }

    public function setCahierCharge(?string $cahierCharge): self
    {
        $this->cahierCharge = $cahierCharge;

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

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setProjet($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getProjet() === $this) {
                $devi->setProjet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Factures>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Factures $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setProjet($this);
        }

        return $this;
    }

    public function removeFacture(Factures $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getProjet() === $this) {
                $facture->setProjet(null);
            }
        }

        return $this;
    }
}
