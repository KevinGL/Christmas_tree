<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: ../login.php");
    die();
}

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM supported_users WHERE user_id = :user_id AND supported_by = :supported_by");
$stmt->execute([
    "user_id" => $_POST["supported_user"],
    "supported_by" => $_SESSION["auth"]
]);

if(!$stmt->fetch())
{
    header("location: ../home.php");
    die();
}

$stmt = $pdo->prepare("UPDATE wish_lists SET name = :name WHERE id = :id");
$stmt->execute([
        "name" => $_POST["name"],
        "id" => $_POST["id"]
    ]);

$_SESSION["flash"] = ["status" => "success", "message" => "Souhait mis à jour"];
header("location: ../home.php");