<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransaksiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaksi::all();
    }
}
