<?php

namespace App\Services\Repository;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileSystemRepository implements RepositoryInterface
{

    /**
     * @param $fileName
     * @return bool
     */
    public function fileExists($fileName): bool
    {
        return Storage::disk('documents')->exists('/' . $fileName);
    }

    /**
     * @param $fileName
     * @return string|null
     */
    public function getFilePath($fileName): ?string
    {
        if ($this->fileExists($fileName)) {
            return Storage::disk('documents')->path('/' . $fileName);
        }

        return null;
    }

    /**
     * @param $fileName
     * @return void
     */
    public function saveFile($fileName): void
    {
        /** @var UploadedFile $fileName */
        $fileName->storeAs('', $fileName->getClientOriginalName(), 'documents');
    }

    /**
     * @param $fileName
     * @return void
     */
    public function deleteFile($fileName): void
    {
        if ($this->fileExists($fileName)) {
            Storage::disk('documents')->delete('/' . $fileName);
        }
    }
}
