<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'cproducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

 
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
{
    return $this->description;
}

public function setDescription(?string $description): static
{
    $this->description = $description;
    return $this;
}
public function getPrice(): ?string
{
    return $this->price;
}

public function setPrice(string|float|null $price): static
{
    $this->price = $price;
    return $this;
}
public function getStock(): ?int
{
    return $this->stock;
}

public function setStock(int $stock): static
{
    $this->stock = $stock;
    return $this;
}

public function getImage(): ?string
{
    return $this->image;
}

public function setImage(string $image): static
{
    $this->image = $image;

    return $this;
}

public function getCategory(): ?Category
{
    return $this->category;
}

public function setCategory(?Category $category): static
{
    $this->category = $category;

    return $this;
}

}
