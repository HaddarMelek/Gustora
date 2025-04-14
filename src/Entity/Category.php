<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    private Collection $cproducts;

    public function __construct()
    {
        $this->cproducts = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Product>
     */
    public function getCproducts(): Collection
    {
        return $this->cproducts;
    }

    public function addCproduct(Product $cproduct): static
    {
        if (!$this->cproducts->contains($cproduct)) {
            $this->cproducts->add($cproduct);
            $cproduct->setCategory($this);
        }

        return $this;
    }

    public function removeCproduct(Product $cproduct): static
    {
        if ($this->cproducts->removeElement($cproduct)) {
            // set the owning side to null (unless already changed)
            if ($cproduct->getCategory() === $this) {
                $cproduct->setCategory(null);
            }
        }

        return $this;
    }
}
