<?php

namespace App\Service\Inventory;

use App\Entity\Product;
use App\Entity\StockMovement;
use App\Entity\User;
use App\Enum\StockMovementType;
use App\Exception\InsufficientStockException;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use App\Service\Notification\AlertService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class StockManagementService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ProductRepository $productRepository,
        private StockMovementRepository $movementRepository,
        private AlertService $alertService,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Ajouter du stock (entrée)
     */
    public function addStock(
        Product $product,
        int $quantity,
        StockMovementType $type,
        User $user,
        ?string $reason = null,
        ?string $reference = null,
        ?float $unitCost = null
    ): StockMovement {
        if (!$type->isIncoming()) {
            throw new \InvalidArgumentException('Type de mouvement invalide pour une entrée de stock');
        }

        $previousStock = $product->getStockQuantity();
        $newStock = $previousStock + $quantity;

        $product->setStockQuantity($newStock);

        $movement = new StockMovement();
        $movement->setProduct($product)
            ->setType($type)
            ->setQuantity($quantity)
            ->setPreviousStock($previousStock)
            ->setNewStock($newStock)
            ->setReason($reason)
            ->setReference($reference)
            ->setCreatedBy($user)
            ->setUnitCost($unitCost ? (string) $unitCost : null)
            ->setTotalCost($unitCost ? (string) ($unitCost * $quantity) : null);

        $this->em->persist($movement);
        $this->em->flush();

        $this->logger->info('Stock ajouté', [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'quantity' => $quantity,
            'type' => $type->value,
            'user' => $user->getEmail(),
        ]);

        // Vérifier si le produit n'est plus en stock bas
        if ($previousStock <= $product->getMinStockLevel() && $newStock > $product->getMinStockLevel()) {
            $this->alertService->resolveLowStockAlert($product);
        }

        return $movement;
    }

    /**
     * Retirer du stock (sortie)
     */
    public function removeStock(
        Product $product,
        int $quantity,
        StockMovementType $type,
        User $user,
        ?string $reason = null,
        ?string $reference = null,
        bool $allowNegative = false
    ): StockMovement {
        if (!$type->isOutgoing()) {
            throw new \InvalidArgumentException('Type de mouvement invalide pour une sortie de stock');
        }

        $previousStock = $product->getStockQuantity();

        if (!$allowNegative && $previousStock < $quantity) {
            throw new InsufficientStockException(
                sprintf(
                    'Stock insuffisant pour le produit "%s". Stock actuel: %d, Quantité demandée: %d',
                    $product->getName(),
                    $previousStock,
                    $quantity
                )
            );
        }

        $newStock = $previousStock - $quantity;

        $product->setStockQuantity($newStock);

        $movement = new StockMovement();
        $movement->setProduct($product)
            ->setType($type)
            ->setQuantity($quantity)
            ->setPreviousStock($previousStock)
            ->setNewStock($newStock)
            ->setReason($reason)
            ->setReference($reference)
            ->setCreatedBy($user);

        $this->em->persist($movement);
        $this->em->flush();

        $this->logger->info('Stock retiré', [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'quantity' => $quantity,
            'type' => $type->value,
            'user' => $user->getEmail(),
        ]);

        // Vérifier si le stock est bas
        if ($newStock <= $product->getMinStockLevel()) {
            $this->alertService->createLowStockAlert($product);
        }

        return $movement;
    }

    /**
     * Ajuster le stock (correction manuelle)
     */
    public function adjustStock(
        Product $product,
        int $newQuantity,
        User $user,
        string $reason
    ): StockMovement {
        $previousStock = $product->getStockQuantity();
        $difference = $newQuantity - $previousStock;

        if ($difference === 0) {
            throw new \InvalidArgumentException('Aucun ajustement nécessaire');
        }

        $type = $difference > 0 ? StockMovementType::ADJUSTMENT_IN : StockMovementType::ADJUSTMENT_OUT;
        $quantity = abs($difference);

        $product->setStockQuantity($newQuantity);

        $movement = new StockMovement();
        $movement->setProduct($product)
            ->setType($type)
            ->setQuantity($quantity)
            ->setPreviousStock($previousStock)
            ->setNewStock($newQuantity)
            ->setReason($reason)
            ->setCreatedBy($user);

        $this->em->persist($movement);
        $this->em->flush();

        $this->logger->warning('Stock ajusté manuellement', [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'previous_stock' => $previousStock,
            'new_stock' => $newQuantity,
            'difference' => $difference,
            'user' => $user->getEmail(),
            'reason' => $reason,
        ]);

        // Vérifier les alertes
        if ($newQuantity <= $product->getMinStockLevel()) {
            $this->alertService->createLowStockAlert($product);
        } elseif ($previousStock <= $product->getMinStockLevel() && $newQuantity > $product->getMinStockLevel()) {
            $this->alertService->resolveLowStockAlert($product);
        }

        return $movement;
    }

    /**
     * Obtenir les produits en stock bas
     */
    public function getLowStockProducts(): array
    {
        return $this->productRepository->findLowStockProducts();
    }

    /**
     * Obtenir les produits en rupture de stock
     */
    public function getOutOfStockProducts(): array
    {
        return $this->productRepository->findOutOfStockProducts();
    }

    /**
     * Obtenir les mouvements de stock pour un produit
     */
    public function getProductMovements(Product $product, int $limit = 50): array
    {
        return $this->movementRepository->findBy(
    ['product' => $product],
    ['createdAt' => 'DESC'],  // ← CORRECTION
    $limit
);
    }

    /**
     * Obtenir le stock total valorisé
     */
    public function getTotalStockValue(): float
    {
        $products = $this->productRepository->findBy(['isActive' => true]);
        $totalValue = 0;

        foreach ($products as $product) {
            $totalValue += $product->getTotalValue();
        }

        return $totalValue;
    }

    /**
     * Obtenir les statistiques de stock
     */
    public function getStockStatistics(): array
    {
        $products = $this->productRepository->findBy(['isActive' => true]);
        
        $stats = [
            'total_products' => count($products),
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'total_items' => 0,
            'total_value' => 0,
            'expired_count' => 0,
            'near_expiry_count' => 0,
        ];

        foreach ($products as $product) {
            $stats['total_items'] += $product->getStockQuantity();
            $stats['total_value'] += $product->getTotalValue();

            if ($product->getStockQuantity() === 0) {
                $stats['out_of_stock_count']++;
            } elseif ($product->isLowStock()) {
                $stats['low_stock_count']++;
            }

            if ($product->isExpired()) {
                $stats['expired_count']++;
            } elseif ($product->isNearExpiry(30)) {
                $stats['near_expiry_count']++;
            }
        }

        return $stats;
    }

    /**
     * Transférer du stock entre produits (si applicable)
     */
    public function transferStock(
        Product $fromProduct,
        Product $toProduct,
        int $quantity,
        User $user,
        string $reason
    ): array {
        $this->em->beginTransaction();

        try {
            $outMovement = $this->removeStock(
                $fromProduct,
                $quantity,
                StockMovementType::TRANSFER_OUT,
                $user,
                $reason,
                'TRANSFER-' . uniqid()
            );

            $inMovement = $this->addStock(
                $toProduct,
                $quantity,
                StockMovementType::TRANSFER_IN,
                $user,
                $reason,
                'TRANSFER-' . uniqid()
            );

            $this->em->commit();

            return [
                'out' => $outMovement,
                'in' => $inMovement,
            ];
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * Marquer des produits comme périmés
     */
    public function markAsExpired(Product $product, int $quantity, User $user): StockMovement
    {
        return $this->removeStock(
            $product,
            $quantity,
            StockMovementType::EXPIRED,
            $user,
            'Produit périmé - Date d\'expiration: ' . $product->getExpiryDate()?->format('d/m/Y'),
            'EXP-' . date('Ymd') . '-' . $product->getId()
        );
    }

    /**
     * Marquer des produits comme endommagés
     */
    public function markAsDamaged(Product $product, int $quantity, User $user, string $reason): StockMovement
    {
        return $this->removeStock(
            $product,
            $quantity,
            StockMovementType::DAMAGED,
            $user,
            $reason,
            'DMG-' . date('Ymd') . '-' . $product->getId()
        );
    }

    /**
     * Vérifier et créer les alertes pour tous les produits
     */
    public function checkAllProductsAlerts(): int
    {
        $alertCount = 0;
        $products = $this->productRepository->findBy(['isActive' => true]);

        foreach ($products as $product) {
            if ($product->isLowStock()) {
                $this->alertService->createLowStockAlert($product);
                $alertCount++;
            }

            if ($product->isExpired()) {
                $this->alertService->createExpiredProductAlert($product);
                $alertCount++;
            } elseif ($product->isNearExpiry(30)) {
                $this->alertService->createNearExpiryAlert($product);
                $alertCount++;
            }
        }

        return $alertCount;
    }
}
