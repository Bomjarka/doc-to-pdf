<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    public function test_index_return_view(): void
    {
        $response = $this->get('/doc-to-pdf');

        $response->assertStatus(200);
        $response->assertViewIs('files');
    }

    public function test_controller_can_upload_file(): void
    {
        Storage::fake('documents');

        $file = UploadedFile::fake()->create('document.docx');
        $data = [
            'file' => $file,
            '_token' => csrf_token(),
        ];

        $response = $this->post('/doc-to-pdf/upload-file', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'vars', 'filename']);
        Storage::disk('documents')->assertExists($file->getClientOriginalName());
    }

    public function test_controller_can_convert_file(): void
    {
        Storage::fake('documents');

        $file = UploadedFile::fake()->create('document.docx');
        $data = [
            'file' => $file,
            '_token' => csrf_token(),
        ];

        $this->post('/doc-to-pdf/upload-file', $data);
        Storage::disk('documents')->assertExists($file->getClientOriginalName());

        $response = $this->post('/doc-to-pdf/convert', array_merge($data, [
                'variable',
                'filename' => $data['file']->getClientOriginalName(),
            ]
        ));
        $convertedFileName = mb_strstr($file->getClientOriginalName(), '.', true) . '.pdf';
        Storage::disk('documents')->assertExists($convertedFileName);

        $response->assertStatus(200);
        $response->assertDownload($convertedFileName);
        $response->sendContent();

        Storage::disk('documents')->assertMissing($file->getClientOriginalName());
        Storage::disk('documents')->assertMissing($convertedFileName);
    }
}
