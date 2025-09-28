<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Series_no = null;

    #[ORM\Column(length: 255)]
    private ?string $Link = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?User $Customer = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    private ?Order $InvoiceOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeriesNo(): ?string
    {
        return $this->Series_no;
    }

    public function setSeriesNo(?string $Series_no): static
    {
        $this->Series_no = $Series_no;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->Link;
    }

    public function setLink(string $Link): static
    {
        $this->Link = $Link;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->Customer;
    }

    public function setCustomer(?User $Customer): static
    {
        $this->Customer = $Customer;

        return $this;
    }

    public function getInvoiceOrder(): ?Order
    {
        return $this->InvoiceOrder;
    }

    public function setInvoiceOrder(?Order $InvoiceOrder): static
    {
        $this->InvoiceOrder = $InvoiceOrder;

        return $this;
    }
}
