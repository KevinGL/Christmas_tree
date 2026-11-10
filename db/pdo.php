<?php

require_once __DIR__ . "/../utils/env.php";

function getPDO()
{
    $pdo = null;
    $vars = getEnvVar();

    $url = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", $vars["DATABASE_HOST"], $vars["DATABASE_PORT"], $vars["DATABASE_NAME"]);

    try
    {
        $pdo = new PDO($url, $vars["DATABASE_USER"], $vars["DATABASE_PASSWORD"], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    catch(Exception $e)
    {
        return null;
    }

    return $pdo;
}