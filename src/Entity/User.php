<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_roles')]
    private $roles;

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'Please enter a password')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Your password should be at least {{ limit }} characters'
    )]
    private ?string $plainPassword = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^\+\d{2,3}$/',
        message: 'Country code must start with + followed by 2 or 3 digits'
    )]
    private ?string $countryCode = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: 'Phone number must be exactly 8 digits'
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $otpCode = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cart::class, orphanRemoval: true)]
    private Collection $carts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Cart::class, cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getOtpCode(): ?int
    {
        return $this->otpCode;
    }

    public function setOtpCode(?int $otpCode): self
    {
        $this->otpCode = $otpCode;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles->map(function ($role) {
            return $role->getRole();
        })->toArray();

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRolesCollection(): ArrayCollection
    {
        return $this->roles;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles->removeElement($role);
    }

    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }
}
