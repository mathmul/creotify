<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false)]
    private ?Order $order = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private ?string $itemType = null; // 'article' or 'subscription'

    #[ORM\Column(type: Types::STRING, length: 20)] // article_id or subscription_package_id
    private ?string $itemId = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    public function __construct(Order $order, string $itemType, string $itemId, string $price)
    {
        $this->order = $order;
        $this->itemType = $itemType;
        $this->itemId = $itemId;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    public function setItemType(string $itemType): static
    {
        $this->itemType = $itemType;

        return $this;
    }

    public function getItemId(): ?string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): static
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }
}
