<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;

class DatabaseController extends Controller
{
    /**
     * Reset database and run seeders
     */
    public function reset(): JsonResponse
    {
        try {
            // Run migrate:fresh --seed
            // We use Symfony Process or Artisan::call.
            // Artisan::call is blocking and runs in same process.
            // For sqlite/postgres in docker, it works as long as ENV matches.
            // But migrate:fresh might drop the connection we are serving from?
            // Laravel handles this usually.

            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Veritabanı sıfırlandı ve örnek veriler yüklendi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }
}
