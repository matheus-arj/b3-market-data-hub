<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function upload(Request $request)
    {
        return $this->uploadService->upload($request);
    }

    public function history(Request $request)
    {
        return $this->uploadService->history($request);
    }

    public function search(Request $request)
    {
        return $this->uploadService->search($request);
    }
}
