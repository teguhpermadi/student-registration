<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum EducationEnum: string implements HasLabel
{
    case SD_sederajat = 'SD sederajat';
    case SMP_sederajat = 'SMP sederajat';
    case SMA_sederajat = 'SMA sederajat';
    case Srata_1 = 'Srata 1';
    case Srata_2 = 'Srata 2';
    case Srata_3 = 'Srata 3';
    
    public function getLabel(): ?string
    {
        return $this->name;
    }
}
