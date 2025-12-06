<?php

namespace App\Entity;

use App\Repository\PrescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $prescriptionNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $prescriptionDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Doctor $doctor = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, PrescriptionItem>
     */
    #[ORM\OneToMany(targetEntity: PrescriptionItem::class, mappedBy: 'prescription', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $prescriptionItems;

    public function __construct()
    {
        $this->prescriptionItems = new ArrayCollection();
        $this->prescriptionDate = new \DateTime();
        $this->status = 'pending';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrescriptionNumber(): ?string
    {
        return $this->prescriptionNumber;
    }

    public function setPrescriptionNumber(string $prescriptionNumber): static
    {
        $this->prescriptionNumber = $prescriptionNumber;
        return $this;
    }

    public function getPrescriptionDate(): ?\DateTimeInterface
    {
        return $this->prescriptionDate;
    }

    public function setPrescriptionDate(\DateTimeInterface $prescriptionDate): static
    {
        $this->prescriptionDate = $prescriptionDate;
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

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): static
    {
        $this->doctor = $doctor;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return Collection<int, PrescriptionItem>
     */
    public function getPrescriptionItems(): Collection
    {
        return $this->prescriptionItems;
    }

    public function addPrescriptionItem(PrescriptionItem $prescriptionItem): static
    {
        if (!$this->prescriptionItems->contains($prescriptionItem)) {
            $this->prescriptionItems->add($prescriptionItem);
            $prescriptionItem->setPrescription($this);
        }
        return $this;
    }

    public function removePrescriptionItem(PrescriptionItem $prescriptionItem): static
    {
        if ($this->prescriptionItems->removeElement($prescriptionItem)) {
            if ($prescriptionItem->getPrescription() === $this) {
                $prescriptionItem->setPrescription(null);
            }
        }
        return $this;
    }
}