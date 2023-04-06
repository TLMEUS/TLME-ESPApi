<?php
    use Dotenv\Dotenv;
    use Database\Database;
    require __DIR__ . "/vendor/autoload.php";
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $conn = $database->getConnection();
        $sql = "INSERT INTO user (name, username, password_hash, api_key) VALUES (:name, :username, :password_hash, :api_key)";
        $stmt = $conn->prepare($sql);
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $api_key = bin2hex(random_bytes(16));
        } catch (Exception $e) {
        }
        $stmt->bindValue(":name", $_POST['name']);
        $stmt->bindValue(":username", $_POST['username']);
        $stmt->bindValue(":password_hash", $password_hash);
        $stmt->bindValue(":api_key", $api_key);
        $stmt->execute();
        echo "Thank you for registering. Your API key is: " . $api_key;
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Register for an API Key</title>
        <link rel="stylesheet" href="/vendor/picocss/pico/css/pico.css">
    </head>
    <body>
        <main class="container">
            <h1>Register for an API key</h1>
            <form method="post">
                <label for="name">
                    Name:
                </label>
                <input name="name" id="name">
                <label for="username">
                    Username
                    <input name="username" id="username">
                </label>
                <label for="password">
                    Password
                    <input name="password" id="password">
                </label>
                <button>Register</button>
            </form>
        </main>
    </body>
</html>