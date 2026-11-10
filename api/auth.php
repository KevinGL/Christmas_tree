<?php

session_start();

require_once "../db/pdo.php";
require_once "../utils/twilio.php";
require_once "../utils/random.php";

if($_SERVER["REQUEST_METHOD"] !== "POST")
{
    $_SESSION["flash"] = ["status" => "error", "message" => "Bad request"];
    header("location: index.php");
    die();
}

if(!isset($_SESSION["auth_code"]))
{
    $phone = $_POST["phone"];
    if($phone[0] === "0")
    {
        $phone = "+33" . substr($phone, 1);
    }

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
}

else
{
    if(!isset($_POST["code"]) || $_POST["code"] === "")
    {
        $_SESSION["flash"] = ["status" => "error", "message" => "Veuillez entrer le code d'authentification !"];
        header("location: ../login.php");
        die();
    }

    if($_POST["code"] === $_SESSION["auth_code"])
    {
        unset($_SESSION["auth_code"]);
        unset($_SESSION["phoneNumber"]);

        $_SESSION["auth"] = true;
    
        header("location: ../home.php");
        die();
    }

    else
    {
        $_SESSION["flash"] = ["status" => "error", "message" => "Mauvaise code entré"];
        header("location: ../login.php");
    }
}