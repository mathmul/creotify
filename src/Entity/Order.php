<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null; // @phpstan-ignore-line

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private ?string $orderNumber = null;

    #[ORM\ManyToOne(inversedBy: 'orders', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'customer_id', nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::STRING, length: 30)]
    private string $status = 'pending';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalPrice = '0.00';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, OrderItem> $orderItems */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderItems;

    public function __construct(Customer $customer)
    {
        $this->orderNumber = uniqid('ORD-');
        $this->customer = $customer;
        $this->createdAt = new \DateTimeImmutable();
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $item): static
    {
        if (!$this->orderItems->contains($item)) {
            $this->orderItems->add($item);
            $item->setOrder($this);
            $this->recalculateTotal();
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $item): static
    {
        if ($this->orderItems->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
            $this->recalculateTotal();
        }

        return $this;
    }

    private function recalculateTotal(): void
    {
        $total = array_sum(array_map(
            fn (OrderItem $i) => (float) $i->getPrice(),
            $this->orderItems->toArray()
        ));

        $this->totalPrice = number_format($total, 2, '.', '');
    }
}
