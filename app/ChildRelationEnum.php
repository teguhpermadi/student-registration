<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum ChildRelationEnum: string implements HasLabel
{
    case ANAK_KANDUK = 'Anak kandung';
    case ANAK_TIRI = 'Anak tiri';
    case ANAK_ANGKAT = 'Anak angkat';
    
    
    public function getLabel(): ?string
    {
        // return $this->name;

        return match ($this) {
            self::ANAK_KANDUK => 'Anak kandung',
            self::ANAK_TIRI => 'Anak tiri',
            self::ANAK_ANGKAT => 'Anak angkat',
        };
    }
}
