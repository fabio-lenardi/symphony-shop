<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $purchasedDate = null;

    #[ORM\Column]
    private ?bool $purchased = null;

    /**
     * @var Collection<int, BasketContent>
     */
    #[ORM\OneToMany(targetEntity: BasketContent::class, mappedBy: 'basket', orphanRemoval: true)]
    private Collection $basketContents;

    public function __construct()
    {
        $this->basketContents = new ArrayCollection();
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

    public function getPurchasedDate(): ?\DateTimeInterface
    {
        return $this->purchasedDate;
    }

    public function setPurchasedDate(?\DateTimeInterface $purchasedDate): static
    {
        $this->purchasedDate = $purchasedDate;

        return $this;
    }

    public function isPurchased(): ?bool
    {
        return $this->purchased;
    }

    public function setPurchased(bool $purchased): static
    {
        $this->purchased = $purchased;

        return $this;
    }

    /**
     * @return Collection<int, BasketContent>
     */
    public function getBasketContents(): Collection
    {
        return $this->basketContents;
    }

    public function addBasketContent(BasketContent $basketContent): static
    {
        if (!$this->basketContents->contains($basketContent)) {
            $this->basketContents->add($basketContent);
            $basketContent->setBasket($this);
        }

        return $this;
    }

    public function removeBasketContent(BasketContent $basketContent): static
    {
        if ($this->basketContents->removeElement($basketContent)) {
            // set the owning side to null (unless already changed)
            if ($basketContent->getBasket() === $this) {
                $basketContent->setBasket(null);
            }
        }

        return $this;
    }
}
