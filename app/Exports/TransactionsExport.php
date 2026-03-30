<?php

namespace App\Exports;

use App\Models\WalletTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return WalletTransaction::all();
    }
}
