<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Process\Process;

class SystemTestController extends Controller
{
    public function run(): JsonResponse
    {
        try {

            file_put_contents(storage_path('logs/laravel.log'), '');

            $process = new Process(['php', 'artisan', 'test', '--colors=never']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(120);
            $process->run();

            $exitCode = $process->getExitCode();
            $cleanOutput = $process->getOutput() . "\n" . $process->getErrorOutput();

            $logs = file_exists(storage_path('logs/laravel.log'))
                ? file_get_contents(storage_path('logs/laravel.log'))
                : '';

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0 ? 'Testler başarılı.' : 'Testler başarısız.',
                'output' => $cleanOutput,
                'logs' => $logs
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test çalıştırılırken hata oluştu: ' . $e->getMessage(),
                'output' => $e->getTraceAsString()
            ], 500);
        }
    }
}
