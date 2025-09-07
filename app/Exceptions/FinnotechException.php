<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom exception class for Finnotech API related errors.
 */
class FinnotechException extends Exception
{
    /**
     * Create a new FinnotechException instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Finnotech API Error',
                'message' => $this->getMessage(),
            ], 500);
        }

        return response()->view('errors.finnotech', ['message' => $this->getMessage()], 500);
    }
} 