<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;

class DocumentController extends Controller
{
    /**
     * @param Request $request
     * @param DocumentService $documentService
     * @return JsonResponse|void
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function upload(Request $request, DocumentService $documentService)
    {
        $document = $request->file('file');
        if ($document) {
            $documentService->saveDocument($document);
            if ($documentService->fileExists($document->getClientOriginalName())) {
                $documentVariables = $documentService->getVariables($document->getClientOriginalName());

                return response()->json([
                    'message' => 'File uploaded',
                    'vars' => $documentVariables,
                    'filename' => $document->getClientOriginalName(),
                ]);
            }
        }

        return 'No file';
    }

    /**
     * @param Request $request
     * @param DocumentService $documentService
     * @return string|void
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function convert(Request $request, DocumentService $documentService)
    {
        $vars = json_decode($request->get('variable'));
        $fileName = $request->get('filename');

        if ($fileName && $documentService->fileExists($fileName)) {
            $documentService->changeTemplate($fileName, $vars);

            return response()->download($documentService->convert($fileName))->deleteFileAfterSend();
        }

        return 'No file';
    }
}

