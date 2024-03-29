<?php

namespace App\Entity;

use App\Repository\SandwichRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SandwichRepository::class)]
class Sandwich
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    private Collection $sandwich_ingredients;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_original = null;

    public function __construct()
    {
        $this->sandwich_ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getSandwichIngredients(): Collection
    {
        return $this->sandwich_ingredients;
    }

    public function addSandwichIngredient(Ingredient $sandwichIngredient): self
    {
        if (!$this->sandwich_ingredients->contains($sandwichIngredient)) {
            $this->sandwich_ingredients->add($sandwichIngredient);
        }

        return $this;
    }

    public function removeSandwichIngredient(Ingredient $sandwichIngredient): self
    {
        $this->sandwich_ingredients->removeElement($sandwichIngredient);

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function calculatePrice():float
    {
        foreach ($this->sandwich_ingredients as $ingredient){
            $this->price += $ingredient->getPrice();
        }
        return $this->price;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function isIsOriginal(): ?bool
    {
        return $this->is_original;
    }

    public function setIsOriginal(?bool $is_original): self
    {
        $this->is_original = $is_original;

        return $this;
    }

}
