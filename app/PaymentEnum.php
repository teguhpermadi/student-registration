<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum PaymentEnum:  string implements HasLabel
{
    case BIAYA_FORMULIR = 'Biaya Formulir';
    case ANGSURAN_PPDB = 'Angsuran PPDB';
    case LUNAS_PPDB = 'Lunas PPDB';
    
    public function getLabel(): ?string
    {
        // return $this->name;

        return match ($this) {
            self::BIAYA_FORMULIR => 'Biaya Formulir',
            self::ANGSURAN_PPDB => 'Angsuran PPDB',
            self::LUNAS_PPDB => 'Lunas PPDB',
        };
    }
}
