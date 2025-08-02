<?php

require __DIR__ . '/../vendor/autoload.php';

use D1Client\D1Connection;

try {
    $conn = new D1Connection(
        "d1_connector.your-worker.workers.dev",
        "root",
        "yourpass",
        "your_db"
    );

    $cursor = $conn->cursor();
    $cursor->execute("SELECT * FROM tracks LIMIT 1");
    $result = $cursor->fetchall();

    echo "Result:\n";
    print_r($result);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
