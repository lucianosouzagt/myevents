<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackPageView::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
            'inactivity.timeout' => \App\Http\Middleware\InactivityTimeout::class,
            'jwt.auth' => \App\Http\Middleware\JwtAuthenticate::class,
            'admin.2fa' => \App\Http\Middleware\AdminTwoFactor::class,
            'admin.timeout' => \App\Http\Middleware\AdminTimeout::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
