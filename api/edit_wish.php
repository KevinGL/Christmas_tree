<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM wish_lists WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    "id" => $_POST["id"],
    "user_id" => $_SESSION["auth"]
]);

$wish = $stmt->fetch();

if(!$wish)
{
    $_SESSION["flash"] = ["status" => "error", "message" => "Une erreur s'est produite"];
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