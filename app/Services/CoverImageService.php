<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoverImageService
{
    public function store(UploadedFile $file, string $title, string $author, int $year): string
    {
        $filename = $this->generateFilename($file, $title, $author, $year);
        $path = $file->storeAs('public/covers', $filename);

        return str_replace('public/', '', $path);
    }

    public function update(?UploadedFile $file, ?string $oldPath, string $title, string $author, int $year): ?string
    {
        if (!$file) {
            return $oldPath;
        }

        // Delete old image if exists
        if ($oldPath) {
            $this->delete($oldPath);
        }

        return $this->store($file, $title, $author, $year);
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::delete('public/' . $path);
        }
    }

    protected function generateFilename(UploadedFile $file, string $title, string $author, int $year): string
    {
        return Str::slug("$title-$author-$year") . '.' . $file->getClientOriginalExtension();
    }
}
