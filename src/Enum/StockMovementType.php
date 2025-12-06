<?php

namespace App\Enum;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';           // Achat fournisseur
    case SALE = 'sale';                   // Vente
    case RETURN_IN = 'return_in';         // Retour client (entrée)
    case RETURN_OUT = 'return_out';       // Retour fournisseur (sortie)
    case ADJUSTMENT_IN = 'adjustment_in'; // Ajustement entrée
    case ADJUSTMENT_OUT = 'adjustment_out'; // Ajustement sortie
    case TRANSFER_IN = 'transfer_in';     // Transfert entrée
    case TRANSFER_OUT = 'transfer_out';   // Transfert sortie
    case EXPIRED = 'expired';             // Produit périmé (sortie)
    case DAMAGED = 'damaged';             // Produit endommagé (sortie)
    case INITIAL = 'initial';             // Stock initial

    public function getLabel(): string
    {
        return match($this) {
            self::PURCHASE => 'Achat',
            self::SALE => 'Vente',
            self::RETURN_IN => 'Retour client',
            self::RETURN_OUT => 'Retour fournisseur',
            self::ADJUSTMENT_IN => 'Ajustement (+)',
            self::ADJUSTMENT_OUT => 'Ajustement (-)',
            self::TRANSFER_IN => 'Transfert entrant',
            self::TRANSFER_OUT => 'Transfert sortant',
            self::EXPIRED => 'Périmé',
            self::DAMAGED => 'Endommagé',
            self::INITIAL => 'Stock initial',
        };
    }

    public function isIncoming(): bool
    {
        return in_array($this, [
            self::PURCHASE,
            self::RETURN_IN,
            self::ADJUSTMENT_IN,
            self::TRANSFER_IN,
            self::INITIAL,
        ]);
    }

    public function isOutgoing(): bool
    {
        return !$this->isIncoming();
    }

    public function getColor(): string
    {
        return match($this) {
            self::PURCHASE, self::RETURN_IN, self::ADJUSTMENT_IN, self::TRANSFER_IN, self::INITIAL => 'success',
            self::SALE, self::RETURN_OUT, self::TRANSFER_OUT => 'primary',
            self::ADJUSTMENT_OUT => 'warning',
            self::EXPIRED, self::DAMAGED => 'danger',
        };
    }
}
