<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/records', function () {
    return response()->json(DB::table('storage_test_table')->get());
});

Route::post('/records', function () {
    $name = request('name', 'unnamed');
    $id = DB::table('storage_test_table')->insertGetId([
        'name' => $name,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['id' => $id, 'name' => $name]);
});

Route::get('/env-check', function () {
    return response()->json([
        'app_key' => config('app.key'),
        'app_name' => config('app.name'),
        'db_connection' => config('database.default'),
        'filesystem' => config('filesystems.default'),
    ]);
});
