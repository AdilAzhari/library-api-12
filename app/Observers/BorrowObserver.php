<?php

namespace App\Observers;

use App\Models\Borrow;

class BorrowObserver
{
    public function saved(Borrow $borrow): void
    {
        $borrow->searchable();
    }

    public function deleted(Borrow $borrow): void
    {
        $borrow->unsearchable();
    }
}
