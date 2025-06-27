<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource]
#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private string $prenom;

    #[ORM\Column(type: 'string', length: 255)]
    private string $adresseLivraison;

    #[ORM\Column(type: 'string', length: 20)]
    private string $numeroTelephone;

    /**
     * @var Collection<int, CarteBancaire>
     */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CarteBancaire::class)]
    private Collection $cartesBancaires;

    public function __construct(string $nom, string $prenom, string $adresseLivraison, ?string $numeroTelephone)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresseLivraison = $adresseLivraison;
        $this->numeroTelephone = $numeroTelephone ?? '';
        $this->cartesBancaires = new ArrayCollection();
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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getAdresseLivraison(): string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getNumeroTelephone(): string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(string $numeroTelephone): self
    {
        $this->numeroTelephone = $numeroTelephone;
        return $this;
    }

    public function getCartesBancaires(): Collection
    {
        return $this->cartesBancaires;
    }
}
