<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/API/bootstrap.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-15
 *
 */

namespace {

    /**
     * Configure Composer Autoload
     */

    require dirname(__DIR__) . "/vendor/autoload.php";

    /**
     *  Set the error handler
     */

    set_error_handler("Handlers\\ErrorHandler::handleError");

    /**
     * Set the exception handler
     */

    set_exception_handler("Handlers\\ErrorHandler::handleException");

    /**
     * Setup to .dotenv object
     */

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

    /**
     * Load the environment settings
     */
    $dotenv->load();

    /**
     * Set the content-type header
     */

    header("Content-type: application/json; charset=UTF-8");
}