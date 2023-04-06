<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/Src/Gateways/CategoryGateway.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-15
 */

namespace Gateways;

/**
 * Import needed classes
 */

use Database\Database;
use PDO;

/**
 * This class provides a gateway class to the Category database table
 */

class CategoryGateway {

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
     * Method to return all categories as an array
     *
     * @return array The returned categories
     */

    public function getAll(): array {
        $sql = "SELECT * FROM category ORDER BY id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['below_cost'] = (bool)$row['below_cost'];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Method to return a single category given an id
     *
     * @param string $id The category id to retrieve
     * @return array|false $data The returned category or false is nothing returned
     */

    public function getSingle(string $id): array {
        $sql = "SELECT * FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data !== false) {
            $data['below_cost'] = (bool) $data['below_cost'];
        }
        return $data;
    }

    /**
     * Method add a new category to the database
     *
     * @param array $data JSON object of the data to insert
     * @return string The last inserted ID
     */

    public function create(array $data): string {
        $sql = "INSERT INTO category (category, below_cost) VALUES (:category, :below_cost)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":category", $data["category"]);
        $stmt->bindValue(":below_cost", $data["below_cost"], PDO::PARAM_BOOL);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    /**
     * Method to update a category in the database
     *
     * @param string $id Method ID to update
     * @param array $data New data for the record
     * @return int Count of the rows updated
     */

    public function update(string $id, array $data): int {
        $fields = [];
        if (!empty($data["category"])) {
            $fields["category"] = [
                $data["category"],
                PDO::PARAM_STR
            ];
        }
        if (array_key_exists("below_cost", $data)) {
            $fields["below_cost"] = [
                $data["below_cost"],
                $data["below_cost"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }
        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE category SET " . implode(", ", $sets) . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            foreach ($fields as $name => $values) {
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    /**
     * Method to delete a category from the database
     *
     * @param string $id  Method ID to delete
     * @return int  Count of the rows deleted
     */

    public function delete(string $id): int {
        $sql = "DELETE FROM category WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}