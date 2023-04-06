<?php
/*
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/Src/Gateways/UserGateway.php class for the TLME-ESP Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-14
 */

namespace Gateways;

use Database\Database;
use PDO;

/**
 * This class provides a gateway to the user table in the database
 */

class UserGateway {

    /**
     * @var PDO The database connection object
     */

    private PDO $conn;

    /**
     * The class constructor
     *
     * @param Database $database The database object
     */

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    /**
     * Method to get a user info with a supplied API Key
     *
     * @param string $key The API Key
     * @return array|false The user info is user exists, false otherwise
     */

    public function getByAPIKey(string $key): array {
        $sql = 'SELECT * FROM user WHERE user.api_key = :api_key';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":api_key", $key);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Method to get user info with a supplied username
     *
     * @param string $username The username
     * @return array|false The user info is user exists, false otherwise
     */

    public function getByUsername(string $username): array {
        $sql = "SELECT * FROM user WHERE user.username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Method to get user info with a supplied user ID
     * @param int $id The user's ID
     * @return array|false The user info is user exists, false otherwise
     */

    public function getByID(int $id): array {
        $sql = "SELECT * FROM user WHERE user.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}