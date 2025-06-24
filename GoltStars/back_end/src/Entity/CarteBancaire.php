<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Client;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource]
#[ORM\Entity]
class CarteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $numeroCarte;

    #[ORM\Column(type: 'string', length: 7)] 
    private string $dateExpiration;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nomTitulaire;

    #[ORM\Column(type: 'string', length: 255)]
    private string $adresseFacturation;

    #[ORM\Column(type: 'string', length: 4)]
    private string $cryptogramme;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'cartesBancaires')]
    private ?Client $client = null;

    public function __construct(string $numeroCarte, string $dateExpiration, string $adresseFacturation, string $cryptogramme, ?Client $client = null)
    {
        $this->numeroCarte = $numeroCarte;
        $this->dateExpiration = $dateExpiration;
        $this->adresseFacturation = $adresseFacturation;
        $this->cryptogramme = $cryptogramme;
        $this->client = $client;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCarte(): string
    {
        return $this->numeroCarte;
    }

    public function setNumeroCarte(string $numeroCarte): self
    {
        $this->numeroCarte = $numeroCarte;
        return $this;
    }

    public function getDateExpiration(): string
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(string $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    public function getNomTitulaire(): string
    {
        return $this->nomTitulaire;
    }

    public function setNomTitulaire(string $nomTitulaire): self
    {
        $this->nomTitulaire = $nomTitulaire;
        return $this;
    }

    public function getAdresseFacturation(): string
    {
        return $this->adresseFacturation;
    }

    public function setAdresseFacturation(string $adresseFacturation): self
    {
        $this->adresseFacturation = $adresseFacturation;
        return $this;
    }

    public function getCryptogramme(): string
    {
        return $this->cryptogramme;
    }

    public function setCryptogramme(string $cryptogramme): self
    {
        $this->cryptogramme = $cryptogramme;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }
} 