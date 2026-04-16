<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déja un compte avec cette email')]
class Inscription implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmpassword = null;

    #[ORM\Column(length: 20)]
    private ?string $userType = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

     

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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmpassword(): ?string
    {
        return $this->confirmpassword;
    }

    public function setConfirmpassword(string $confirmpassword): static
    {
        $this->confirmpassword = $confirmpassword;

        return $this;
    }
    

    public function getRoles(): array
    {
        // Retourner un tableau de rôles pour l'utilisateur (ex. ['ROLE_USER'])
        $roles = $this->roles;
        
        // S'assurer que 'ROLE_USER' est toujours présent
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

         return array_unique($roles);
        
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getSalt(): ?string
    {
        // Si vous utilisez bcrypt ou argon2i, il n'est pas nécessaire d'avoir un salt supplémentaire
        return null;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des informations sensibles temporairement, nettoyez-les ici
        // Par exemple, si vous stockez le mot de passe en clair temporairement
        // $this->plainPassword = null;
    }
    public function getUserIdentifier(): string
    {
        // Retourne une chaîne unique pour identifier l'utilisateur
        // Vous pouvez utiliser l'email ou le nom d'utilisateur
        return $this->email;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;
        // Définir les rôles en fonction du type d'utilisateur
        $roles = $userType === 'enseignant' ? ['ROLE_ENSEIGNANT'] : ['ROLE_ELEVE'];
        $this->setRoles($roles); 
        return $this;
    }

    public function makeAdmin(): self
    {
        $currentRoles = $this->getRoles();
        if (!in_array('ROLE_ADMIN', $currentRoles)) {
            $currentRoles[] = 'ROLE_ADMIN';
            $this->setRoles($currentRoles);
        }
        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
