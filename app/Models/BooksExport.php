<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class BooksExport implements FromCollection
{
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return Book::all();
    }
}
