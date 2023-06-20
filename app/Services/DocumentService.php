<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\Converter\ConverterInterface;
use App\Services\Repository\RepositoryInterface;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;


class DocumentService
{
    public function __construct(
        private ConverterInterface  $converter,
        private RepositoryInterface $fileRepository,
    )
    {
    }

    /**
     * @param UploadedFile $document
     * @return void
     */
    public function saveDocument(UploadedFile $document): void
    {
        $this->fileRepository->saveFile($document);
    }

    /**
     * @param string $documentName
     * @return bool
     */
    public function fileExists(string $documentName): bool
    {
        return $this->fileRepository->fileExists($documentName);
    }

    /**
     * @param string $documentName
     * @return string
     */
    private function getFilePath(string $documentName): string
    {
        return $this->fileRepository->getFilePath($documentName);
    }


    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function changeTemplate(string $documentName, $vars = null): void
    {
        $templateProcessor = new TemplateProcessor($this->getFilePath($documentName));
        if ($vars) {
            foreach ($vars as $var => $val) {
                $templateProcessor->setValue($var, $val);
            }
        }
        $templateProcessor->saveAs($this->getFilePath($documentName));
    }


    public function convert(string $documentName)
    {
        $convertedFile = $this->converter->convert($this->getFilePath($documentName));
        $this->deleteFile($documentName);

        return $convertedFile;
    }

    /**
     * @param string $documentName
     * @return void
     */
    private function deleteFile(string $documentName): void
    {
        $this->fileRepository->deleteFile($documentName);
    }

    /**
     * @param string $documentName
     * @return string[]
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function getVariables(string $documentName): array
    {
        return (new TemplateProcessor($this->getFilePath($documentName)))->getVariables();
    }
}
