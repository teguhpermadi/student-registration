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
        return $this->name;
    }
}
