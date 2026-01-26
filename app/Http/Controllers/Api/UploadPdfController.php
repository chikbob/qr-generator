<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadPdfController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', // макс 5Мб
        ]);

        $file = $request->file('file');
        $path = $file->store('public/uploads/pdfs');

        // Получаем публичный URL (предполагаем, что настроен symlink storage)
        $url = Storage::url($path);

        return response()->json(['url' => $url]);
    }
}
