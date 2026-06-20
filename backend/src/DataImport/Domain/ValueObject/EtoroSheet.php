<?php

declare(strict_types=1);

namespace App\DataImport\Domain\ValueObject;

enum EtoroSheet: string
{
    case ClosedPositions = 'closed_positions';
    case AccountActivity = 'account_activity';
    case Dividends = 'dividends';

    public function label(): string
    {
        return match ($this) {
            self::ClosedPositions => 'Pozycje zamknięte',
            self::AccountActivity => 'Aktywność na rachunku',
            self::Dividends => 'Dywidendy',
        };
    }

    /**
     * @return list<EtoroSheet>
     */
    public static function all(): array
    {
        return [
            self::ClosedPositions,
            self::AccountActivity,
            self::Dividends,
        ];
    }
}
