<?php

session_start();

//Trop de tentatives échouées
if(isset($_SESSION["forbidden"]))
{
    $now = new DateTime();

    //Suppression du blocage au bout d'un certain temps
    if($now->getTimestamp() - $_SESSION["forbidden"]["ts"] > 3600)
    {
        unset($_SESSION["forbidden"]);
    }

    else
    {
        $_SESSION["flash"] = ["status" => "error", "message" => "Trop de tentatives réalisées"];
        header("location: ../login.php");
        die();
    }
}

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
    $now = new DateTime();

    sendMessage($phone, "Bienvenue " . $user['username'] . " ! Voici ton code d'accès : " . $code);
    
    //Sauvegarde code généré, nombre de tentatives de connexion et timestamp courant
    $_SESSION["auth_code"] = ["code" => $code, "nbTry" => 0, "ts" => $now->getTimestamp()];
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

    $now = new DateTime();

    //Code expiré
    if($now->getTimestamp() - $_SESSION["auth_code"]["ts"] > 1800)
    {
        unset($_SESSION["auth_code"]);
        unset($_SESSION["phoneNumber"]);

        $_SESSION["flash"] = ["status" => "error", "message" => "Code d'accès expiré"];

        header("location: ../login.php");
        die();
    }

    //Accès autorisé
    if($_POST["code"] === $_SESSION["auth_code"]["code"])
    {
        $pdo = getPDO();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone");
        $stmt->execute(["phone" => $_SESSION["phoneNumber"]]);
        $user = $stmt->fetch();

        unset($_SESSION["auth_code"]);
        unset($_SESSION["phoneNumber"]);

        $_SESSION["auth"] = $user["id"];
        $_SESSION["flash"] = ["status" => "success", "message" => "Bienvenue " . $user["username"] . " !"];
    
        header("location: ../home.php");
        die();
    }

    else
    {
        $_SESSION["flash"] = ["status" => "error", "message" => "Mauvaise code entré"];
        $_SESSION["auth_code"]["nbTry"]++;

        //Nombre maximal de tentatives dépassé
        if($_SESSION["auth_code"]["nbTry"] > 3)
        {
            $now = new DateTime();
        
            $_SESSION["forbidden"] = ["status" => true, "ts" => $now->getTimestamp()];
            $_SESSION["flash"] = ["status" => "error", "message" => "Trop de tentatives réalisées"];

            unset($_SESSION["auth_code"]);
            unset($_SESSION["phoneNumber"]);
            
            header("location: ../login.php");
            die();
        }

        header("location: ../login.php");
    }
}