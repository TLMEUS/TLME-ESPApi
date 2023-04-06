<?php
/**
 * This file contains the C:/TLME/Projects/ESP/TLME-ESP-API/Src/Gateways/PlanGateway.php class for the TLME-ESP  Api
 *
 * PHP Version 7.4
 *
 * @author troylmarker
 * @version 1.0
 * @since 2023-3-24
 */

namespace Gateways;

/**
 * Import needed classes
 */

use Database\Database;
use PDO;

/**
 * This class provided a gateway class to the Plan database table
 */

class PlanGateway {

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
     * Method to return all plans in a given category
     *
     * @param string $category The category id
     * @return array The returned plans
     */

    public function getAllForCategory(string $category): array {
        $sql = "SELECT * FROM plan WHERE parent = :category ORDER BY id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":category", $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Method to return a single plan given a category and plan id
     *
     * @param string $category The category id
     * @param string $id The plan id
     * @return array|false $data The returned plan or false is nothing returned
     */

    public function getSingle(string $category, string $id)
    {
        $sql = "SELECT * FROM plan WHERE parent = :category AND id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":category", $category);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Method to add a plan to the database
     *
     * @param array $data The plan data
     * @return string The last inserted plan id
     * @noinspection SqlInsertValues
     */

    public function create(string $parent, array $data): string {
        $sql = "INSERT INTO plan (id, parent, name, min, max, tier1term, tier1cost, tier1sku, tier2term, tier2cost, tier2sku) VALUES 
               (:id, :parent, :name, :min, :max, :tier1term, :tier1cost, :tier1sku, :tier2term, :tier2cost, :tier2sku)";
        $stmt = $this->conn->prepare($sql);
        $new_id = $this->getNextPlanId($parent);
        $stmt->bindValue(":id", $new_id);
        $stmt->bindValue(":parent", $parent);
        $stmt->bindValue(":name", $data['name']);
        $stmt->bindValue(":min", $data['min']);
        $stmt->bindValue(":max", $data['max']);
        $stmt->bindValue(":tier1term", $data['tier1term']);
        $stmt->bindValue(":tier1cost", $data['tier1cost']);
        $stmt->bindValue(":tier1sku", $data['tier1sku']);
        $stmt->bindValue(":tier2term", $data['tier2term']);
        $stmt->bindValue(":tier2cost", $data['tier2cost']);
        $stmt->bindValue(":tier2sku", $data['tier2sku']);
        $stmt->execute();
        return $parent . "-" . $new_id;
    }

    /**
     * Method to update a plan in the database
     *
     * @param string $id The plan ID to update
     * @param array $data The new plan data
     * @return int Count of rows updated
     */

    public function update(string $parent, string $id, array $data):int {
        $fields = [];
        if (!empty($data["name"])) {
            $fields["name"] = [
                $data["name"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["min"])) {
            $fields["min"] = [
                $data["min"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["max"])) {
            $fields["max"] = [
                $data["max"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["tier1term"])) {
            $fields["tier1term"] = [
                $data["tier1term"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["tier1cost"])) {
            $fields["tier1cost"] = [
                $data["tier1cost"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["tier1sku"])) {
            $fields["tier1sku"] = [
                $data["tier1sku"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["tier2term"])) {
            $fields["tier2term"] = [
                $data["tier2term"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["tier2cost"])) {
            $fields["tier2cost"] = [
                $data["tier2cost"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["tier2sku"])) {
            $fields["tier2sku"] = [
                $data["tier2sku"],
                PDO::PARAM_INT
            ];
        }
        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE plan SET " . implode(", ", $sets) . " WHERE id = :id and parent = :parent";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":parent", $parent, PDO::PARAM_INT);
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

    public function delete(string $category, string $id): int {
        $sql = "DELETE FROM plan WHERE id = :id AND parent = :parent";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":parent", $category, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Private method to get the next available plan id given a category
     *
     * @param string $parent The parent category
     * @return string The next plan id number
     */

    private function getNextPlanId(string $parent):string {
        $sql = "SELECT count(*) FROM plan WHERE parent = :parent";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":parent", $parent);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result + 1;
    }
}