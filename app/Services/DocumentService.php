<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;


class DocumentService
{

    private const DOCUMENTS_FOLDER = 'documents';
    /**
     * @param UploadedFile $document
     * @return void
     */
    public function saveDocument(UploadedFile $document): void
    {
        $document->storeAs('documents', $document->getClientOriginalName(), 'local');
    }

    /**
     * @param string $documentName
     * @return bool
     * @throws FilesystemException
     */
    public function fileExists(string $documentName): bool
    {
        return Storage::disk('local')->has(self::DOCUMENTS_FOLDER . '/' . $documentName);
    }

    /**
     * @param string $documentName
     * @return string
     */
    private function getFile(string $documentName): string
    {
        return Storage::disk('local')->path(self::DOCUMENTS_FOLDER . '/' . $documentName);
    }


    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function changeTemplate(string $documentName, $vars = null): void
    {
        $templateProcessor = new TemplateProcessor($this->getFile($documentName));
        if ($vars) {
            foreach ($vars as $var => $val) {
                $templateProcessor->setValue($var, $val);
            }
        }
        $templateProcessor->saveAs($this->getFile($documentName));
    }

    /**
     * @param string $documentName
     * @return string
     * @throws Exception
     */
    public function convertToPDF(string $documentName): string
    {
        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');

        //Load word file
        $content = IOFactory::load($this->getFile($documentName));

        //Save it into PDF
        $PDFWriter = IOFactory::createWriter($content, 'PDF');
        $PDFWriter->save(Storage::disk('local')->path(self::DOCUMENTS_FOLDER . '/' . mb_strstr($documentName, '.', true) . '.pdf'));

        $this->deleteFile($documentName);

        return Storage::disk('local')->path(self::DOCUMENTS_FOLDER . '/' . mb_strstr($documentName, '.', true) . '.pdf');
    }

    /**
     * @param string $documentName
     * @return void
     */
    private function deleteFile(string $documentName): void
    {
        if (Storage::disk('local')->exists(self::DOCUMENTS_FOLDER . '/' . $documentName)) {
            Storage::delete(self::DOCUMENTS_FOLDER . '/' . $documentName);
        }
    }

    /**
     * @param string $documentName
     * @return string[]
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function getVariables(string $documentName): array
    {
        $templateProcessor = new TemplateProcessor($this->getFile($documentName));

        return $templateProcessor->getVariables();
    }
}
