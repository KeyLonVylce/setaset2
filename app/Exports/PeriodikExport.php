<?php

namespace App\Exports;
use App\Models\PindahBarang;

use Maatwebsite\Excel\Concerns\FromCollection;

class PeriodikExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
        public function collection()
    {
        return PindahBarang::with(['barang','asal','tujuan'])->get();
    }
}
