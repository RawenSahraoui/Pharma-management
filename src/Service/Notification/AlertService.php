<?php

namespace App\Service\Notification;

class AlertService
{
    public function sendLowStockAlert(int $productId, int $quantity): bool
    {
        // TODO: Implement low stock alert
        return true;
    }

    public function sendExpiryAlert(int $productId, \DateTimeInterface $expiryDate): bool
    {
        // TODO: Implement expiry alert
        return true;
    }

    public function sendAlert(string $type, array $data): bool
    {
        // TODO: Implement generic alert sending
        return true;
    }

    public function checkLowStock(): array
    {
        // TODO: Implement low stock check
        return [];
    }

    public function checkExpiring(int $daysThreshold = 30): array
    {
        // TODO: Implement expiring products check
        return [];
    }
}