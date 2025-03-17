<?php

namespace App\Services;

use App\Models\BooksExport;
use Vtiful\Kernel\Excel;

class BookExportService
{
    public function exportBooks()
    {
        return Excel::download(new BooksExport, 'books.xlsx');
    }
}
