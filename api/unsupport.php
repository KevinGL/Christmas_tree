<?php

session_start();

require_once __DIR__ . "/../db/pdo.php";
require_once __DIR__ . "/../safety/encrypt.php";

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

$w = $stmt->fetch();

//Introuvable
if(!$w)
{
    header("location: ../home.php");
    die();
}

$supportedByArray = explode(",", decryptData($w["supported"]));

if(!in_array($_SESSION["auth"], $supportedByArray))
{
    header("location: ../home.php");
    die();
}

array_splice($supportedByArray, array_search($_SESSION["auth"], $supportedByArray), 1);

$supportedBy = encryptData(implode(",", $supportedByArray));

$stmt = $pdo->prepare("UPDATE wish_lists SET supported = :supported WHERE id = :id");
$stmt->execute([
    "supported" => $supportedBy,
    "id" => $_GET["id"]
]);

header("location: ../home.php");