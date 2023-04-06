<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/src/Authorization/Auth.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-15
 */

namespace Authorization;

/**
 * Import need classes
 */

use Gateways\UserGateway;

/**
 * This class provides the authorization methods to the API
 */
class Auth {

    /**
     * @var int The user_id property
     */

    private int $user_id;

    /**
     * @var UserGateway The user_gateway property
     */

    private UserGateway $user_gateway;

    /**
     * The class constructor
     *
     * @param UserGateway $user_gateway The UserGateway property
     */
    public function __construct(UserGateway $user_gateway) {
        $this->user_gateway = $user_gateway;
    }

    /**
     * Method to check if the supplied API key is valid.
     * A valid user id will be assigned to the $user_id property.
     *
     * @return bool Returns true is valid API key, of FALSE on failure
     */
    public function authenticateAPIKey(): bool {
        if(empty($_SERVER["HTTP_X_API_KEY"])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing API key"]);
            return false;
        }

        $api_key = $_SERVER["HTTP_X_API_KEY"];

        $user = $this->user_gateway->getByAPIKey($api_key);

        if($user === false) {
            return false;
        }
        $this->user_id = $user["id"];
        return true;
    }

    /**
     * The User ID getter
     *
     * @return int The authorized user ID
     */
    public function getUserID() : int {
        return $this->user_id;
    }
}