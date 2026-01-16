<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API istekleri için özel Handler kullan
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return app(Handler::class)->handleException($e);
            }
            return null; // Web istekleri için default davranış
        });
    })->create();
