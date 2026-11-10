<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";

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

$stmt = $pdo->prepare("INSERT INTO wish_lists (name, user_id) VALUES (:name, :user_id)");
$stmt->execute(["name" => $_POST["name"], "user_id" => $_SESSION["auth"]]);

header("location: ../home.php");