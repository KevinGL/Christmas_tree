<?php

require_once __DIR__ . "/../db/pdo.php";

function getOthersUsers(): array
{
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id <> :id");
    $stmt->execute(["id" => $_SESSION["auth"]]);

    return $stmt->fetchAll();
}