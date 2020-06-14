<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BienRepository")
 */
class Bien
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visiteLien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visiteDossier;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nombreVue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $debutPromo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $finPromo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleMap;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombreProduit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mode")
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="biens")
     */
    private $partenaire;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", inversedBy="biens")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProduitMagasin", mappedBy="bien")
     */
    private $produitMagasins;

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
        $this->produitMagasins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getVisiteLien(): ?string
    {
        return $this->visiteLien;
    }

    public function setVisiteLien(?string $visiteLien): self
    {
        $this->visiteLien = $visiteLien;

        return $this;
    }

    public function getVisiteDossier(): ?string
    {
        return $this->visiteDossier;
    }

    public function setVisiteDossier(?string $visiteDossier): self
    {
        $this->visiteDossier = $visiteDossier;

        return $this;
    }

    public function getNombreVue(): ?int
    {
        return $this->nombreVue;
    }

    public function setNombreVue(?int $nombreVue): self
    {
        $this->nombreVue = $nombreVue;

        return $this;
    }

    public function getDebutPromo(): ?string
    {
        return $this->debutPromo;
    }

    public function setDebutPromo(?string $debutPromo): self
    {
        $this->debutPromo = $debutPromo;

        return $this;
    }

    public function getFinPromo(): ?string
    {
        return $this->finPromo;
    }

    public function setFinPromo(?string $finPromo): self
    {
        $this->finPromo = $finPromo;

        return $this;
    }

    public function getGoogleMap(): ?string
    {
        return $this->googleMap;
    }

    public function setGoogleMap(?string $googleMap): self
    {
        $this->googleMap = $googleMap;

        return $this;
    }

    public function getNombreProduit(): ?string
    {
        return $this->nombreProduit;
    }

    public function setNombreProduit(?string $nombreProduit): self
    {
        $this->nombreProduit = $nombreProduit;

        return $this;
    }

    public function getMode(): ?Mode
    {
        return $this->mode;
    }

    public function setMode(?Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        if ($this->categorie->contains($categorie)) {
            $this->categorie->removeElement($categorie);
        }

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|ProduitMagasin[]
     */
    public function getProduitMagasins(): Collection
    {
        return $this->produitMagasins;
    }

    public function addProduitMagasin(ProduitMagasin $produitMagasin): self
    {
        if (!$this->produitMagasins->contains($produitMagasin)) {
            $this->produitMagasins[] = $produitMagasin;
            $produitMagasin->setBien($this);
        }

        return $this;
    }

    public function removeProduitMagasin(ProduitMagasin $produitMagasin): self
    {
        if ($this->produitMagasins->contains($produitMagasin)) {
            $this->produitMagasins->removeElement($produitMagasin);
            // set the owning side to null (unless already changed)
            if ($produitMagasin->getBien() === $this) {
                $produitMagasin->setBien(null);
            }
        }

        return $this;
    }
}
