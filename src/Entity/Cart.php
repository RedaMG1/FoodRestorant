<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class)]
    private Collection $cartItems;

    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Ordering::class)]
    private Collection $orderings;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->orderings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): static
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setCart($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): static
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getCart() === $this) {
                $cartItem->setCart(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, Ordering>
     */
    public function getOrderings(): Collection
    {
        return $this->orderings;
    }

    public function addOrdering(Ordering $ordering): static
    {
        if (!$this->orderings->contains($ordering)) {
            $this->orderings->add($ordering);
            $ordering->setCart($this);
        }

        return $this;
    }

    public function removeOrdering(Ordering $ordering): static
    {
        if ($this->orderings->removeElement($ordering)) {
            // set the owning side to null (unless already changed)
            if ($ordering->getCart() === $this) {
                $ordering->setCart(null);
            }
        }

        return $this;
    }
}
