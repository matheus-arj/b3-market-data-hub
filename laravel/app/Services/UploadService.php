<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Record;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class UploadService
{
    public function upload(Request $request)
    {
        Log::debug("Starting file upload.");

        $file = $request->file('file');

        if (!$file) {
            return response()->json(['message' => 'No file provided.'], 400);
        }

        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, ['csv', 'xls', 'xlsx'])) {
            return response()->json(['message' => 'Invalid file type. Only CSV and Excel files are allowed.'], 400);
        }

        $hash = $this->generateFileHash($file->getRealPath());

        if ($this->isDuplicate($hash)) {
            return response()->json(['message' => 'File already uploaded.'], 409);
        }

        $storedName = $this->generateStoredFileName($file->getClientOriginalName());

        if (!$this->storeFile($file, $storedName)) {
            return response()->json(['message' => 'Failed to move uploaded file.'], 500);
        }

        $upload = Upload::create([
            'original_name'  => $file->getClientOriginalName(),
            'stored_name'    => $storedName,
            'hash'           => $hash,
            'reference_date' => $this->extractReferenceDate($file->getClientOriginalName()),
        ]);

        Log::debug('Upload saved:', $upload->toArray());

        return response()->json(['message' => 'File uploaded successfully.'], 201);
    }

    private function generateFileHash(string $filePath): string
    {
        return md5_file($filePath);
    }

    private function isDuplicate(string $hash): bool
    {
        return Upload::where('hash', $hash)->exists();
    }

    private function generateStoredFileName(string $originalName): string
    {
        return time() . '_' . $originalName;
    }

    private function storeFile($file, string $storedName): bool
    {
        $destination = public_path('uploads');
        log::debug($destination); 

        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        try {
            $file->move($destination, $storedName);
            return true;
        } catch (\Exception $e) {
            Log::error('Error moving file: ' . $e->getMessage());
            return false;
        }
    }

    private function extractReferenceDate(string $filename): ?string
    {
        if (preg_match('/_(\d{8})_/', $filename, $matches)) {
            $rawDate = $matches[1];
            return substr($rawDate, 0, 4) . '-' . substr($rawDate, 4, 2) . '-' . substr($rawDate, 6, 2);
        }

        return null;
    }

    public function history(Request $request)
    {
        Log::debug("Fetching upload history.");

        $query = Upload::query();

        if ($request->filled('original_name')) {
            $query->where('original_name', 'like', '%' . $request->original_name . '%');
        }

        if ($request->filled('reference_date')) {
            $query->where('reference_date', $request->reference_date);
        }

        return response()->json($query->get());
    }

}