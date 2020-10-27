<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 *
 * @OA\Schema
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("product:read")
     *
     * @OA\Property (type="integer", property="id", description="Product unique ID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("product:read")
     *
     * @OA\Property (type="string", description="Product model")
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("product:read")
     *
     * @OA\Property (type="string", description="Product price")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("product:read")
     *
     * @OA\Property (type="string", description="Product brand")
     */
    private $brand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
