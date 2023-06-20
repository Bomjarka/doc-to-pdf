<?php

namespace Tests\Unit;

use App\Services\Converter\PDFConverter;
use App\Services\DocumentService;
use App\Services\Repository\FileSystemRepository;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    /**
     * @param $fileName
     * @return File
     */
    private function createFile($fileName = null): File
    {
        return UploadedFile::fake()->create($fileName ?? Str::random(10) . '.docx');
    }

    /**
     * @param $className
     * @param $methodName
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    private function getPrivateMethod($className, $methodName): ReflectionMethod
    {
        $reflector = new ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    public function test_service_can_save_document(): void
    {
        $documentService = new DocumentService(new PDFConverter(), new FileSystemRepository());
        Storage::fake('documents');
        $fileName = 'document.docx';

        $file = $this->createFile($fileName);

        $documentService->saveDocument($file);

        Storage::disk('documents')->assertExists($file->getClientOriginalName());
    }

    public function test_service_can_check_is_file_exists(): void
    {
        $documentService = new DocumentService(new PDFConverter(), new FileSystemRepository());
        Storage::fake('documents');
        $fileExistsName = 'document.docx';
        $fileNotExistsName = 'test.docx';

        $fileExists = $this->createFile($fileExistsName);
        $fileNotExists = $this->createFile($fileNotExistsName);

        $documentService->saveDocument($fileExists);

        $this->assertTrue($documentService->fileExists($fileExists->getClientOriginalName()));
        $this->assertFalse($documentService->fileExists($fileNotExists->getClientOriginalName()));
    }

    public function test_service_can_get_file_path(): void
    {
        $documentService = new DocumentService(new PDFConverter(), new FileSystemRepository());
        Storage::fake('documents');

        $file = $this->createFile();
        $documentService->saveDocument($file);

        $getFilePath = $this->getPrivateMethod($documentService, 'getFilePath');
        $storagePath = Storage::disk('documents')->path($file->getClientOriginalName());

        $fileSystemRepositoryPath = $getFilePath->invokeArgs($documentService, [$file->getClientOriginalName()]);

        $this->assertEquals($storagePath, $fileSystemRepositoryPath);
    }

    public function test_service_can_delete_file(): void
    {
        $documentService = new DocumentService(new PDFConverter(), new FileSystemRepository());
        Storage::fake('documents');

        $file = $this->createFile();
        $documentService->saveDocument($file);
        Storage::disk('documents')->assertExists($file->getClientOriginalName());

        $deleteFile = $this->getPrivateMethod($documentService, 'deleteFile');
        $deleteFile->invokeArgs($documentService, [$file->getClientOriginalName()]);
        Storage::disk('documents')->assertMissing($file->getClientOriginalName());
    }
}
