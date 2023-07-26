<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'Cart')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'SET NULL')]

    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(name: 'commande_id', referencedColumnName: 'id', onDelete: 'SET NULL')]

    private ?Order $commande = null;

    #[ORM\ManyToOne(inversedBy: 'Cart')]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $totalP = null;

    #[ORM\Column]
    private ?bool $isEnabled = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCommande(): ?Order
    {
        return $this->commande;
    }

    public function setCommande(?Order $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalP(): ?float
    {
        return $this->totalP;
    }

    public function setTotalP(float $totalP): static
    {
        $this->totalP = $totalP;

        return $this;
    }

    public function isIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}
