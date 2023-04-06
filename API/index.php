<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/API/index.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-15
 */

declare(strict_types=1);
/**
 * Load needed classes
 */

use Controllers\CategoryController;
use Controllers\PlanController;
use Database\Database;
use Gateways\CategoryGateway;
use Gateways\PlanGateway;
use Gateways\UserGateway;
use Authorization\Auth;

/**
 * Load the bootstrap file
 */

require __DIR__ . "/bootstrap.php";

/**
 * Get the path from the URI
 */

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

/**
 * Get the request method
 */

$method = $_SERVER["REQUEST_METHOD"];

/**
 * Break the path apart
 */

$parts = explode("/", $path);

/**
 * Assign part one to the resource
 */

$resource = $parts[1];

/**
 * Determine $id and $category values dependent on the value of $resource
 */

if($resource == "category") {
    $id = $parts[2] ?? null;
}
if($resource == "plan") {
    $category = $parts[2];
    $id = $parts[3] ?? null;
}

/**
 * Create the database object
 */

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

/**
 * Check for an authorized user
 */

$user_gateway = new UserGateway($database);
$auth = new Auth($user_gateway);
if (!$auth->authenticateAPIKey()) {
    http_response_code(401);
    echo json_encode(["message" => "You are not authorized to access this API."]);
    exit;
}

/**
 * Prepare the request
 */

switch ($resource) {
    case "category":
        $controller_gateway = new CategoryGateway($database);
        $controller = new CategoryController($controller_gateway);
        $controller->processRequest($method, $id);
        break;
    case "plan":
        $controller_gateway = new PlanGateway($database);
        $controller = new PlanController($controller_gateway);
        $controller->processRequest($method, $category, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "That resource is not available on this server."]);
        exit;
    }