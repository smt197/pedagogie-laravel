<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
    {
        // Si l'exception est une exception d'autorisation
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => 'error',
                'message' => "Vous n'avez pas l'autorisation d'accéder à cette ressource.",
                'code' => 403
            ], 403);
        }

        // Pour d'autres types d'exceptions, laissez la gestion par défaut ou personnalisez-la
        return parent::render($request, $exception);
    }


}
