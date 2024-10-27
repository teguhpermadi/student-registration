<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum ChildRelationEnum: string implements HasLabel
{
    case Anak_kandung = 'Anak kandung';
    case Anak_tiri = 'Anak tiri';
    case Anak_angkat = 'Anak angkat';
    
    
    public function getLabel(): ?string
    {
        return $this->name;
    }
}
