<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);

    $middleware->redirectGuestsTo(function (Request $request) {

        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        if ($request->is('landlord/*')) {
            return route('landlord.login');
        }

        return route('landlord.login');
    });

})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();