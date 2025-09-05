<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BooksExport;
use Vtiful\Kernel\Excel;

final class BookExportService
{
    public function exportBooks()
    {
        return Excel::download(new BooksExport, 'books.xlsx');
    }
}
