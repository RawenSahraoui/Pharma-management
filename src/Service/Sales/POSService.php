<?php

namespace App\Service\Sales;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class POSService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository
    ) {
    }

    public function searchProducts(string $query): array
    {
        return $this->productRepository->searchForPOS($query);
    }

    public function getProductByBarcode(string $barcode): ?Product
    {
        return $this->productRepository->findOneBy(['barcode' => $barcode]);
    }

    public function processSale(array $items, float $totalAmount, string $paymentMethod): bool
    {
        // TODO: Implement sale processing logic
        // This will be implemented later with Sale entity
        return true;
    }

    public function calculateTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        return $total;
    }
}