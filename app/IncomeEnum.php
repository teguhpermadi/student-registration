<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum IncomeEnum: string implements HasLabel
{
    case TIDAK_BERPENGHASILAN = 'TIDAK BERPENGHASILAN';
    case KURANG_DARI_RP_500_000 = 'KURANG DARI RP 500.000';
    case RP_500_000_SAMPAI_RP_1_000_000 = 'RP 500.000 SAMPAI RP 1.000.000';
    case RP_1_000_000_SAMPAI_RP_2_000_000 = 'RP 1.000.000 SAMPAI RP 2.000.000';
    case RP_2_000_000_SAMPAI_RP_3_000_000 = 'RP 2.000.000 SAMPAI RP 3.000.000';
    case RP_3_000_000_SAMPAI_RP_5_000_00 = 'RP 3.000.000 SAMPAI RP 5.000.000';
    case LEBIH_DARI_RP_5_000_000 = 'LEBIH DARI RP 5.000.000';
    
    public function getLabel(): ?string
    {
        // return $this->name;

        return match ($this) {
            self::TIDAK_BERPENGHASILAN => 'TIDAK BERPENGHASILAN',
            self::KURANG_DARI_RP_500_000 => 'KURANG DARI RP 500.000',
            self::RP_500_000_SAMPAI_RP_1_000_000 => 'RP 500.000 SAMPAI RP 1.000.000',
            self::RP_1_000_000_SAMPAI_RP_2_000_000 => 'RP 1.000.000 SAMPAI RP 2.000.000',
            self::RP_2_000_000_SAMPAI_RP_3_000_000 => 'RP 2.000.000 SAMPAI RP 3.000.000',
            self::RP_3_000_000_SAMPAI_RP_5_000_00 => 'RP 3.000.000 SAMPAI RP 5.000.000',
            self::LEBIH_DARI_RP_5_000_000 => 'LEBIH DARI RP 5.000.000',
        };
    }
}
