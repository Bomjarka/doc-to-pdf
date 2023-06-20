<?php

namespace Tests\Unit;

use App\Services\Repository\FileSystemRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FileSystemRepositoryTest extends TestCase
{
    private function createFile($fileName = null)
    {
        return UploadedFile::fake()->create($fileName ?? Str::random(10) . '.docx');
    }

    public function test_repository_can_save_file(): void
    {
        $fileSystemRepository = new FileSystemRepository();
        Storage::fake('documents');
        $fileName = 'document.docx';

        $file = $this->createFile($fileName);

        $fileSystemRepository->saveFile($file);

        Storage::disk('documents')->assertExists($file->getClientOriginalName());
    }

    public function test_repository_can_check_is_file_exists(): void
    {
        $fileSystemRepository = new FileSystemRepository();
        Storage::fake('documents');
        $fileExistsName = 'document.docx';
        $fileNotExistsName = 'test.docx';

        $fileExists = $this->createFile($fileExistsName);
        $fileNotExists = $this->createFile($fileNotExistsName);

        $fileSystemRepository->saveFile($fileExists);

        $this->assertTrue($fileSystemRepository->fileExists($fileExists->getClientOriginalName()));
        $this->assertFalse($fileSystemRepository->fileExists($fileNotExists->getClientOriginalName()));
    }

    public function test_repository_can_get_file_path(): void
    {
        $fileSystemRepository = new FileSystemRepository();
        Storage::fake('documents');

        $file = $this->createFile();
        $fileSystemRepository->saveFile($file);


        $storagePath = Storage::disk('documents')->path($file->getClientOriginalName());
        $fileSystemRepositoryPath = $fileSystemRepository->getFilePath($file->getClientOriginalName());

        $this->assertEquals($storagePath, $fileSystemRepositoryPath);
    }

    public function test_repository_can_delete_file(): void
    {
        $fileSystemRepository = new FileSystemRepository();
        Storage::fake('documents');

        $file = $this->createFile();
        $fileSystemRepository->saveFile($file);
        Storage::disk('documents')->assertExists($file->getClientOriginalName());

        $fileSystemRepository->deleteFile($file->getClientOriginalName());
        Storage::disk('documents')->assertMissing($file->getClientOriginalName());
    }
}
