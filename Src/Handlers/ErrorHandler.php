<?php
/*
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/Src/Handlers/ErrorHandler.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-14
 */

namespace Handlers;

/**
 * Import required classes
 */

use ErrorException;
use Throwable;

/**
 * Error Handler class
 */

class ErrorHandler {

    /**
     * handleError Method
     *
     * This method turns an error into an exception, for the exception handler to handle it.
     *
     * @param int $errno The error number
     * @param string $errstr The error text
     * @param string $errfile The file the error is in
     * @param int $errline The line number containing the error
     * @return void
     * @throws ErrorException
     */

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * handleException static method
     *
     * This static method handles an exception by throwing an exception in error data to be displayed in JSON format.
     *
     * @param Throwable $exception
     * @return void
     */

    public static function handleException(Throwable $exception): void {

        http_response_code(500);
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}