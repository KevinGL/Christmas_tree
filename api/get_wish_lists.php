<?php

require_once __DIR__ . "/../db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

function getWishLists(): array
{
    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT w.id as wish_id, w.name AS wish_name, w.supported as wish_supported, u.username as username FROM wish_lists w JOIN users u ON w.user_id = u.id WHERE w.user_id <> :id");
    $stmt->execute(["id" => $currentId]);
    $wishs = $stmt->fetchAll();

    return $wishs;
}

function getOwnList(): array
{
    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT id, name FROM wish_lists WHERE user_id = :id");
    $stmt->execute(["id" => $currentId]);

    return $stmt->fetchAll();
}

function getSupportedUsers(): array
{
    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT u.id, u.username FROM supported_users su JOIN users u ON su.user_id = u.id WHERE su.supported_by = :supported_by");
    $stmt->execute(["supported_by" => $currentId]);

    return $stmt->fetchAll();
}

function getWishsSupportedUsers(): array
{
    $usersSupported = getSupportedUsers();
    $pdo = getPDO();
    
    $res = [];
    foreach($usersSupported as $u)
    {
        $stmt = $pdo->prepare("SELECT id, name, supported FROM wish_lists WHERE user_id = :user_id");
        $stmt->execute(["user_id" => $u["id"]]);

        $wishs = $stmt->fetchAll();

        if($wishs)
        {
            foreach($wishs as $w)
            {
                $res[] = [...$w, "supported_user" => $u["id"]];
            }
        }
    }

    return $res;
}