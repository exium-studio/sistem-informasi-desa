<?php

namespace App\Http\Controllers\Arsip;

use App\Helpers\StorageServerHelper;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function test(Request $request)
    {
        $file = $request->file('dokumen');
        $filename = Str::random(35);
        $dataupload = StorageServerHelper::uploadToServer($request, $filename);
        $data['dokumen'] = $dataupload['path'];

        Document::create([

        ]);
    }
}
