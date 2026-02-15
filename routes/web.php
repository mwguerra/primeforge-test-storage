<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $files = [];
    try {
        $files = collect(Storage::disk('s3')->allFiles())
            ->map(fn (string $path) => [
                'name' => $path,
                'url' => Storage::disk('s3')->url($path),
                'size' => Storage::disk('s3')->size($path),
            ])
            ->toArray();
    } catch (\Throwable $e) {
        $files = [];
    }

    $disk = config('filesystems.default');
    $endpoint = config('filesystems.disks.s3.endpoint', 'not set');
    $bucket = config('filesystems.disks.s3.bucket', 'not set');

    return view('welcome', compact('files', 'disk', 'endpoint', 'bucket'));
});

Route::post('/upload', function () {
    $file = request()->file('file');

    if (! $file) {
        return back()->with('error', 'No file selected.');
    }

    $path = Storage::disk('s3')->putFile('uploads', $file);

    return back()->with('success', "File uploaded: {$path}");
});

Route::delete('/files/{path}', function (string $path) {
    Storage::disk('s3')->delete($path);

    return back()->with('success', "File deleted: {$path}");
})->where('path', '.*');
