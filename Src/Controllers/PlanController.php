<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/Src/Controllers/PlanController.php class for the TLME-ESP  Api
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

use Gateways\PlanGateway;

/**
 * PlanController
 *
 * This controller contains methods to retrieve information from the plan table in the database
 *
 */

class PlanController {

    /**
     * @var PlanGateway The PlanGateway property
     */

    private PlanGateway $gateway;

    /**
     * The class constructor
     *
     * @param PlanGateway $gateway The Plan Gateway
     */

    public function __construct(PlanGateway $gateway) {
        $this->gateway = $gateway;
    }

    /**
     * processRequest Method
     *
     * This method processes the Plan request
     *
     * @param string $method
     * @param string|null $category
     * @param string|null $plan
     * @return void
     */

    public function processRequest(string $method, ?string $category, ?string $plan): void {
        if ($plan === null) {
            switch ($method) {
                case "GET":
                    echo json_encode($this->gateway->getAllForCategory($category));
                    break;
                case "POST":
                    $data = (array)json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($data);
                    if (!empty($error)) {
                        $this->planUnprocessableEntity($errors);
                        return;
                    }
                    $id = $this->gateway->create($category, $data);
                    $this->planCreated($id);
                    break;
                default:
                    $this->planMethodNotAllowed("GET, POST");
                    break;
            }
        } else {
            $plansearch = $this->gateway->getSingle($category, $plan);
            if ($plansearch === false) {
                $this->planNotFound($category, $plan);
                return;
            }
            switch ($method) {
                case "GET":
                    echo json_encode($plansearch);
                    break;
                case "PATCH":
                    $data = (array)json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($data, false);
                    if (!empty($errors)) {
                        $this->planUnprocessableEntity($errors);
                        return;
                    }
                    $rows = $this->gateway->update($category, $plan, $data);
                    http_response_code(200);
                    echo json_encode(["message" => "Plan updated", "rows" => $rows]);
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete($category, $plan);
                    http_response_code(200);
                    echo json_encode(["message" => "Plan deleted.", "rows" => $rows]);
                    break;
                default:
                    $this->planMethodNotAllowed("GET, PATCH, DELETE");
                    break;
            }
        }
    }

    /**
     * planUnprocessableEntity Method
     *
     * This private method displays validation errors contained in the Plan Data that caused the add function to fail.
     *
     * @param array $errors The list of errors
     * @return void
     */

    private function planUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
    }

    /**
     * planMethodNotAllowed Method
     *
     * This private method display the allowed Request Method when an incorrect Request is made.
     *
     * @param string $allowed_methods The list of allowed methods
     * @return void
     */

    private function planMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
        echo json_encode(['Allowed Methods' => $allowed_methods]);
    }

    /**
     * planNotFound Method
     *
     * This private method displays an error when a plan is not found in the database.
     *
     * @param string $category The category containing the id
     * @param string $id The id of the missing plan
     * @return void
     */

    private function planNotFound(string $category, string $id): void
    {
        http_response_code(404);
        echo json_encode(["message" => "Plan in category $category with ID $id not found"]);
    }

    /**
     * planCreated Method
     *
     * This private method show a success message when the plan is created.
     *
     * @param string $id The id of the created plan.
     * @return void
     */

    private function planCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Plan created", "Category-Plan" => $id]);
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

    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];
        if (strlen($data['name']) > 100) {
            $errors [] = "Plan length is to long. Max of 100 characters.";
        }
        if (!empty($data['min']) && !is_double($data['min']) ) {
            $errors[] = "Minimum cost is not a valid value.";
        }
        if (!empty($data['max']) && !is_double('max')) {
            $errors[] = 'Maximum cost is not a valid value.';
        }
        if($is_new && empty($data['tier1term'])) {
            $errors[] = 'Tier 1 term is required.';
        }
        if (!empty($data['tier1term']) && strlen($data['tier1term'] > 10)) {
            $errors[] = 'Tier 1 Term is too long.';
        }
        if (!empty($data['tier1cost']) && !is_double($data['tier1cost'])) {
            $errors[] ='Tier 1 cost is not a valid value';
        }
        if (!empty($data['tier1sku']) && !is_int($data['tier1sku'])) {
            $errors[] = 'Tier 1 sku is nat a valid value.';
        }
        if (!empty($data['tier2term'])) {
            if (!empty($data['tier2term']) && strlen($data['tier2term'] > 10)) {
                $errors[] = 'Tier 2 Term is too long.';
            }
            if (!empty($data['tier2cost']) && !is_double($data['tier2cost'])) {
                $errors[] ='Tier 2 cost is not a valid value';
            }
            if (!empty($data['tier2sku']) && !is_int($data['tier2sku'])) {
                $errors[] = 'Tier 2 sku is nat a valid value.';
            }
        }
        return $errors;
    }

}