<?php

namespace App\Services\Repository;

interface RepositoryInterface
{
    public function getFilePath($fileName);

    public function saveFile($fileName);

    public function fileExists($fileName);

    public function deleteFile($fileName);
}
