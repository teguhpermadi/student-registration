<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum ParentStatusEnum: string implements HasLabel
{
    case Alive = 'Hidup';
    case Dead = 'Wafat';
    case Divorced = 'Berpisah';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Alive => 'Hidup',
            self::Dead => 'Wafat',
            self::Divorced => 'Berpisah',
        };
    }
}
