<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilleRepository")
 */
class Famille
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
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nombreProduit;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProduitMagasin", mappedBy="famille")
     */
    private $produitMagasins;

    public function __construct()
    {
        $this->produitMagasins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getNombreProduit(): ?int
    {
        return $this->nombreProduit;
    }

    public function setNombreProduit(?int $nombreProduit): self
    {
        $this->nombreProduit = $nombreProduit;

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
            $produitMagasin->addFamille($this);
        }

        return $this;
    }

    public function removeProduitMagasin(ProduitMagasin $produitMagasin): self
    {
        if ($this->produitMagasins->contains($produitMagasin)) {
            $this->produitMagasins->removeElement($produitMagasin);
            $produitMagasin->removeFamille($this);
        }

        return $this;
    }
}
