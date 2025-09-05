<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Borrow;

final class BorrowObserver
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
