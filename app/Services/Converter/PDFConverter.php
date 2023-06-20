<?php

namespace App\Services\Converter;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class PDFConverter implements ConverterInterface
{

    private const FILE_EXTENSION = '.pdf';

    /**
     * @param $documentPath
     * @return string
     * @throws Exception
     */
    public function convert($documentPath): string
    {
        $savePath = mb_strstr($documentPath, '.', true) . self::FILE_EXTENSION;
        /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');
        //Load word file
        $content = IOFactory::load($documentPath);
        //Save it into PDF
        $PDFWriter = IOFactory::createWriter($content, 'PDF');
        $this->save($PDFWriter, $savePath);

        return $savePath;
    }

    /**
     * @param $PDFWriter
     * @param $savePath
     * @return void
     */
    private function save($PDFWriter, $savePath): void
    {
        $PDFWriter->save($savePath);
    }
}
