<?php

namespace App\Entity;

use App\Repository\OrderingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderingRepository::class)]
class Ordering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 3, nullable: true)]
    private ?string $amount = null;

    #[ORM\Column(length: 30)]
    private ?string $created_at = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $shipped_at = null;

    #[ORM\ManyToOne(inversedBy: 'orderings')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'ordering', targetEntity: Payement::class)]
    private Collection $payements;

    #[ORM\ManyToOne(inversedBy: 'orderings')]
    private ?Cart $cart = null;

    public function __construct()
    {
        $this->payements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getShippedAt(): ?string
    {
        return $this->shipped_at;
    }

    public function setShippedAt(?string $shipped_at): static
    {
        $this->shipped_at = $shipped_at;

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

    /**
     * @return Collection<int, Payement>
     */
    public function getPayements(): Collection
    {
        return $this->payements;
    }

    public function addPayement(Payement $payement): static
    {
        if (!$this->payements->contains($payement)) {
            $this->payements->add($payement);
            $payement->setOrdering($this);
        }

        return $this;
    }

    public function removePayement(Payement $payement): static
    {
        if ($this->payements->removeElement($payement)) {
            // set the owning side to null (unless already changed)
            if ($payement->getOrdering() === $this) {
                $payement->setOrdering(null);
            }
        }

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }
}
