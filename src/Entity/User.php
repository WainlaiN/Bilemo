<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="email dejà utilisé")
 *
 * @Hateoas\Relation(
 *     "SELF",
 *      href="expr('/api/user/' ~ object.getId())",
 *     exclusion = @Hateoas\Exclusion(groups={"default"})
 *     )
 * @Hateoas\Relation(
 *     "POST",
 *     href = "/api/user",
 *     exclusion = @Hateoas\Exclusion(groups={"default"})
 * )
 * @Hateoas\Relation(
 *     "DELETE",
 *     href = "expr('/api/user/' ~ object.getId())",
 *     exclusion = @Hateoas\Exclusion(groups={"default"})
 * )
 *
 * @OA\Schema
 */
class User
{
    /**
     * Unique ID for User
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @OA\Property (type="int", property="id" ,description="User unique ID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @Serializer\Groups({"default"})
     *
     * @OA\Property (type="string", description="username")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Groups({"default"})
     *
     * @OA\Property (type="string", description="email")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     *
     * @Serializer\Exclude()
     */
    private $client;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(UserInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->client->contains($client)) {
            $this->client->removeElement($client);
            $client->removeUser($this);
        }

        return $this;
    }
}
