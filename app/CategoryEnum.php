<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum CategoryEnum: string implements HasLabel
{
    case REGULAR = 'Regular';
    case INKLUSI = 'Inklusi';

    public function getLabel(): ?string
    {
        // return $this->name;

        return match ($this) {
            self::REGULAR => 'Regular',
            self::INKLUSI => 'Inklusi',
        };
    }
}
