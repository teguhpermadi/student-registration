<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum JobEnum: string implements HasLabel
{
    case TIDAK_BEKERJA = 'TIDAK_BEKERJA';
    case PENSIUNAN = 'PENSIUNAN';
    case TNI = 'TNI';
    case POLRI = 'POLRI';
    case GURU = 'GURU';
    case DOSEN = 'DOSEN';
    case PEGAWAI_NEGERI = 'PEGAWAI NEGERI';
    case PEGAWAI_SWASTA = 'PEGAWAI SWASTA';
    case WIRASWASTA = 'WIRASWASTA';
    case LAINNYA = 'LAINNYA';
    
    public function getLabel(): ?string
    {
        // return $this->name;

        return match ($this) {
            self::TIDAK_BEKERJA => 'TIDAK_BEKERJA',
            self::PENSIUNAN => 'PENSIUNAN',
            self::TNI => 'TNI',
            self::POLRI => 'POLRI',
            self::GURU => 'GURU',
            self::DOSEN => 'DOSEN',
            self::PEGAWAI_NEGERI => 'PEGAWAI NEGER',
            self::PEGAWAI_SWASTA => 'PEGAWAI SWAST',
            self::WIRASWASTA => 'WIRASWASTA',
            self::LAINNYA => 'LAINNYA',
        };
    }
}
