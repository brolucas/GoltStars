<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Produit
{
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'float')]
    private float $prix;

    #[ORM\Column(type:'string', length : 255)]
    private string $url;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'produits')]
    private Collection $categories;

    public function __construct(string $nom, float $prix, string $url)
    {
        $this->nom = $nom;
        $this->prix = $prix;
        $this->url = $url;
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categories->contains($categorie)) {
            $this->categories[] = $categorie;
            $categorie->addProduit($this);
        }
        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        if ($this->categories->removeElement($categorie)) {
            $categorie->removeProduit($this);
        }
        return $this;
    }
}
