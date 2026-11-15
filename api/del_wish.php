<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: ../login.php");
    die();
}

if(!isset($_GET["id"]))
{
    header("location: ../home.php");
    die();
}

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM wish_lists WHERE id = :id");
$stmt->execute([
    "id" => $_GET["id"]
]);

$wish = $stmt->fetch();
$userIds = explode(",", $wish["user_id"]);

if(!$wish || !in_array($_SESSION["auth"], $userIds))
{
    header("location: ../home.php");
    die();
}

$stmt = $pdo->prepare("DELETE FROM wish_lists WHERE id = :id");
$stmt->execute(["id" => $wish["id"]]);

header("location: ../home.php");