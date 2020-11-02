<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @UniqueEntity(fields="email", message="Ce client existe dejà.", groups={"register"})
 *
 * @OA\Schema
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @OA\Property (type="int", property="id" ,description="Client unique ID")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Groups("client:read")
     * @Assert\Email(message="email non valide", groups={"register"})
     *
     * @OA\Property (type="string", description="email")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("client:read")
     * @Assert\NotBlank(message="le Nom ne doit pas être vide", groups={"register"})
     *
     * @OA\Property (type="string", description="client name")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="client", cascade={"persist", "remove"})
     * @Groups("client:read")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="8 caractères minumum", groups={"register"})
     */
    private $password;

    /** @ORM\Column(type="json") */
    private $roles = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function setRoles()
    {
        $this->roles = ['ROLE_USER'];
    }
}
