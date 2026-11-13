<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";
require_once __DIR__ . "/../safety/encrypt.php";

if(!isset($_SESSION["auth"]))
{
    header("location: ../login.php");
    die();
}

if(!isset($_POST["name"]))
{
    $_SESSION["flash"] = ["status" => "error", "message" => "Merci de préciser le cadeau voulu"];

    header("location: ../home.php");
    die();
}

$pdo = getPDO();

$userId = isset($_GET["user_id"]) ? $_GET["user_id"] : $_SESSION["auth"];

if(isset($_GET["user_id"]))
{
    $stmt = $pdo->prepare("SELECT * FROM supported_users WHERE user_id = :user_id AND supported_by = :supported_by");
    $stmt->execute([
        "user_id" => $_GET["user_id"],
        "supported_by" => $_SESSION["auth"]
    ]);

    //Utilisateur non pris en charge par utilisateur courant
    if(!$stmt->fetch())
    {
        header("location: ../home.php");
        die();
    }
}

$stmt = $pdo->prepare("INSERT INTO wish_lists (name, supported, user_id) VALUES (:name, :supported, :user_id)");
$stmt->execute([
    "name" => $_POST["name"],
    "supported" => encryptData(""),
    "user_id" => $userId
]);

header("location: ../home.php");