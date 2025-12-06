<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ['name'], name: 'idx_product_name')]
#[ORM\Index(columns: ['barcode'], name: 'idx_product_barcode')]
#[UniqueEntity(fields: ['barcode'], message: 'Ce code-barres existe déjà')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Le code-barres est obligatoire')]
    private ?string $barcode = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sku = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La catégorie est obligatoire')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'Le prix d\'achat doit être positif')]
    private ?string $purchasePrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'Le prix de vente doit être positif')]
    #[Assert\GreaterThan(propertyPath: 'purchasePrice', message: 'Le prix de vente doit être supérieur au prix d\'achat')]
    private ?string $sellingPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'La TVA doit être entre {{ min }}% et {{ max }}%')]
    private ?string $taxRate = '19.00';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $stockQuantity = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive(message: 'Le seuil minimum doit être positif')]
    private ?int $minStockLevel = 10;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $maxStockLevel = 1000;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unit = 'unité';

    #[ORM\Column(nullable: true)]
    private ?int $packSize = 1;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $manufacturer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiryDate = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $batchNumber = null;

    #[ORM\Column]
    private ?bool $requiresPrescription = false;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $composition = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dosage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sideEffects = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contraindications = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $storageInstructions = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, StockMovement>
     */
    #[ORM\OneToMany(targetEntity: StockMovement::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $stockMovements;

    public function __construct()
    {
        $this->stockMovements = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): static
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): static
    {
        $this->sku = $sku;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getPurchasePrice(): ?string
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(string $purchasePrice): static
    {
        $this->purchasePrice = $purchasePrice;
        return $this;
    }

    public function getSellingPrice(): ?string
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(string $sellingPrice): static
    {
        $this->sellingPrice = $sellingPrice;
        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->taxRate;
    }

    public function setTaxRate(?string $taxRate): static
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }

    public function getMinStockLevel(): ?int
    {
        return $this->minStockLevel;
    }

    public function setMinStockLevel(int $minStockLevel): static
    {
        $this->minStockLevel = $minStockLevel;
        return $this;
    }

    public function getMaxStockLevel(): ?int
    {
        return $this->maxStockLevel;
    }

    public function setMaxStockLevel(int $maxStockLevel): static
    {
        $this->maxStockLevel = $maxStockLevel;
        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;
        return $this;
    }

    public function getPackSize(): ?int
    {
        return $this->packSize;
    }

    public function setPackSize(?int $packSize): static
    {
        $this->packSize = $packSize;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): static
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getBatchNumber(): ?string
    {
        return $this->batchNumber;
    }

    public function setBatchNumber(?string $batchNumber): static
    {
        $this->batchNumber = $batchNumber;
        return $this;
    }

    public function isRequiresPrescription(): ?bool
    {
        return $this->requiresPrescription;
    }

    public function setRequiresPrescription(bool $requiresPrescription): static
    {
        $this->requiresPrescription = $requiresPrescription;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getComposition(): ?string
    {
        return $this->composition;
    }

    public function setComposition(?string $composition): static
    {
        $this->composition = $composition;
        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(?string $dosage): static
    {
        $this->dosage = $dosage;
        return $this;
    }

    public function getSideEffects(): ?string
    {
        return $this->sideEffects;
    }

    public function setSideEffects(?string $sideEffects): static
    {
        $this->sideEffects = $sideEffects;
        return $this;
    }

    public function getContraindications(): ?string
    {
        return $this->contraindications;
    }

    public function setContraindications(?string $contraindications): static
    {
        $this->contraindications = $contraindications;
        return $this;
    }

    public function getStorageInstructions(): ?string
    {
        return $this->storageInstructions;
    }

    public function setStorageInstructions(?string $storageInstructions): static
    {
        $this->storageInstructions = $storageInstructions;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, StockMovement>
     */
    public function getStockMovements(): Collection
    {
        return $this->stockMovements;
    }

    public function addStockMovement(StockMovement $stockMovement): static
    {
        if (!$this->stockMovements->contains($stockMovement)) {
            $this->stockMovements->add($stockMovement);
            $stockMovement->setProduct($this);
        }
        return $this;
    }

    public function removeStockMovement(StockMovement $stockMovement): static
    {
        if ($this->stockMovements->removeElement($stockMovement)) {
            if ($stockMovement->getProduct() === $this) {
                $stockMovement->setProduct(null);
            }
        }
        return $this;
    }

    // Méthodes utilitaires

    public function isLowStock(): bool
    {
        return $this->stockQuantity <= $this->minStockLevel;
    }

    public function isExpired(): bool
    {
        if (!$this->expiryDate) {
            return false;
        }
        return $this->expiryDate < new \DateTime();
    }

    public function isNearExpiry(int $days = 30): bool
    {
        if (!$this->expiryDate) {
            return false;
        }
        $threshold = new \DateTime("+{$days} days");
        return $this->expiryDate <= $threshold;
    }

    public function getProfitMargin(): float
    {
        $purchase = (float) $this->purchasePrice;
        $selling = (float) $this->sellingPrice;
        if ($purchase == 0) {
            return 0;
        }
        return (($selling - $purchase) / $purchase) * 100;
    }

    public function getTotalValue(): float
    {
        return (float) $this->sellingPrice * $this->stockQuantity;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}