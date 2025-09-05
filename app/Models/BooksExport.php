<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

final class BooksExport implements FromCollection
{
    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return Book::all();
    }
}
