<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum ReligionEnum: string implements HasLabel
{
    case Islam = 'Islam';
    case Kristen = 'Kristen';
    case Katholik = 'Katholik';
    case Hindu = 'Hindu';
    case Budha = 'Budha';
    case Konghucu = 'Konghucu';
    
    
    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::Islam => 'Islam',
            self::Kristen => 'Kristen',
            self::Katholik => 'Katholik',
            self::Hindu => 'Hindu',
            self::Budha => 'Budha',
            self::Konghucu => 'Konghucu',
        };
    }
}
