<?php
define('DB_HOST','localhost');
define('DB_NAME','stresser_db');
define('DB_USER','root');
define('DB_PASS','');

function dbConnect(){
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    return $pdo;
}
?>
