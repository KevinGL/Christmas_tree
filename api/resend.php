<?php

session_start();

require_once "../db/pdo.php";
require_once "../utils/twilio.php";
require_once "../utils/random.php";

if(!isset($_SESSION["phoneNumber"]))
{
    $_SESSION["flash"] = ["status" => "error", "message" => "Une erreur s'est produite"];
    unset($_SESSION["auth_code"]);
    unset($_SESSION["phoneNumber"]);

    header("location: index.php");
    die();
}

$phone = $_SESSION["phoneNumber"];

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone");
$stmt->execute(["phone" => $phone]);
$user = $stmt->fetch();

if(!$user)
{
    $_SESSION["flash"] = ["status" => "error", "message" => "Navré vous ne faites pas partie de la famille !"];
    header("location: ../index.php");
    die();
}

$code = getRandomCode(6);

sendMessage($phone, "Bienvenue " . $user['username'] . " ! Voici ton code d'accès : " . $code);
$_SESSION["auth_code"] = $code;
$_SESSION["phoneNumber"] = $phone;

header("location: ../login.php");
die();