<?php

namespace Tests\Unit;

use App\Services\Converter\PDFConverter;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Tests\TestCase;

class PDFConverterTest extends TestCase
{
    /**
     * @param $fileName
     * @return File
     */
    private function createFile($fileName = null): File
    {
        return UploadedFile::fake()->create($fileName ?? Str::random(10) . '.docx');
    }

    public function test_converter_can_convert_document(): void
    {
        $pdfConverter = new PDFConverter();
        Storage::fake('documents');
        $file = $this->createFile();
        Storage::disk('documents')->put($file->getClientOriginalName(), '');
        $convertedFileName = mb_strstr($file->getClientOriginalName(), '.', true) . '.pdf';

        $pdfConverter->convert(Storage::disk('documents')->path($file->getClientOriginalName()));
        Storage::disk('documents')->assertExists($convertedFileName);
    }
}
