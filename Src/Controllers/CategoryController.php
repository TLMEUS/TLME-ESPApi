<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/src/Controllers/CategoryController.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-24
 */

namespace Controllers;

/**
 * Import needed classes
 */

use Gateways\CategoryGateway;

/**
 * CategoryController
 *
 * This controller contains methods to retrieve information from the category table in the database
 *
 */

class CategoryController {

    /**
     * @var CategoryGateway The CategoryGateway property
     */

    private CategoryGateway $gateway;

    /**
     * The class constructor
     *
     * @param CategoryGateway $gateway The Category Gateway
     */

    public function __construct(CategoryGateway $gateway){
        $this->gateway = $gateway;
    }

    /**
     * processRequest Method
     *
     * This method processes the Catagory request
     *
     * @param string $method
     * @param string|null $id
     * @return void
     */

    public function processRequest(string $method, ?string $id): void {
        if ($id === null) {
            if ($method == "GET") {
                echo json_encode($this->gateway->getAll());
            } elseif ($method == "POST") {
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    $this->categoryUnprocessableEntity($errors);
                    return;
                }
                $id = $this->gateway->create($data);
                $this->categoryCreated($id);
            } else {
                $this->categoryMethodNotAllowed("GET, POST");
            }
        } else {
            $category = $this->gateway->getSingle($id);
            if ($category === false) {
                $this->categoryNotFound($id);
                return;
            }
            switch ($method) {
                case "GET":
                    echo json_encode($category);
                    break;
                case "PATCH":
                    $data = (array)json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($data, false);
                    if (!empty($errors)) {
                        $this->categoryUnprocessableEntity($errors);
                        return;
                    }
                    $rows = $this->gateway->update($id, $data);
                    http_response_code(200);
                    echo json_encode(["message" => "Category updated", "rows" => $rows]);
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete($id);
                    http_response_code(200);
                    echo json_encode(["message" => "Category deleted", "rows" => $rows]);
                    break;
                default:
                    $this->categoryMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }
        }
    }

    /**
     * categoryUnprocessableEntity Method
     *
     * This privite method displays validation errors contained int the Category Data that caused the add function to fail.
     *
     * @param array $errors The list of errors
     * @return void
     */

    private function categoryUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
    }

    /**
     * categoryMethodNotAllowed Method
     *
     * This private method display the allowed Request Method when an incorrect Request is made.
     *
     * @param string $allowed_methods The list of allowed methods
     * @return void
     */

    private function categoryMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
        echo json_encode(['Allowed Methods' => $allowed_methods]);
    }

    /**
     * categoryNotFound Method
     *
     * This private method displays an error when a category is not found in the database.
     *
     * @param string $id The id of the missing category
     * @return void
     */

    private function categoryNotFound(string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Category with ID $id not found"]);
    }

    /**
     * categoryCreated Method
     *
     * This private method show a success message when the category is created.
     *
     * @param string $id The id of the created category.
     * @return void
     */

    private function categoryCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Category created", "id" => $id]);
    }

    /**
     * getValidationErrors Method
     *
     * This private method checks the data array for errors
     *
     * @param array $data The post data array
     * @param bool $is_new Flag indicating if the category is new
     * @return array Array containing all validation errors
     */

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        if ($is_new && empty($data['category'])) {
            $errors [] = "Category is required.";
        }
        if (strlen($data['category']) > 50) {
            $errors [] = "Category length is to long. Max of 50 characters.";
        }
        if (!empty($data["below_cost"])) {
            if (filter_var($data['below_cost'], FILTER_VALIDATE_INT) === false || ($data['below_cost'] < 0 || $data['below_cost'] > 1)) {
                $errors[] = "Below Cost must be an integer 0 for False or 1 for True.";
            }
        }
        return $errors;
    }
}