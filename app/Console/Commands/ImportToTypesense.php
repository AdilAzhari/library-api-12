<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Console\Command;

final class ImportToTypesense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'typesense:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data to Typesense';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Importing books...');
        Book::all()->searchable();

        $this->info('Importing borrowings...');
        Borrow::with(['book', 'user'])->get()->searchable();

        $this->info('Import completed!');
    }
}
