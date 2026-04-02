<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: ['webhooks/*']);

        $middleware->alias([
            'role' => \App\Http\Middleware\Role::class,
            'active' => \App\Http\Middleware\ActiveUser::class,
            'can-admin' => \App\Http\Middleware\CheckAdminPermission::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ActiveUser::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
