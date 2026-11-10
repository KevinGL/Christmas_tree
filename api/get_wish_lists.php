<?php

require_once __DIR__ . "/../db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

function getWishLists(): array
{
    $res = [];

    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT w.name AS wish_name, w.supported as wish_supported, u.username as username FROM wish_lists w JOIN users u ON w.user_id = u.id WHERE w.user_id <> :id");
    $stmt->execute(["id" => $currentId]);
    $wishs = $stmt->fetchAll();

    foreach($wishs as $w)
    {
        $res[$w["username"]][] = $w["wish_name"];
    }

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id <> :id");
    $stmt->execute(["id" => $currentId]);
    $users = $stmt->fetchAll();

    foreach($users as $user)
    {
        $username = $user["username"];
    
        if(!isset($res[$username]))
        {
            $res[$username] = [];
        }
    }

    return $res;
}

function getOwnList(): array
{
    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT id, name FROM wish_lists WHERE user_id = :id");
    $stmt->execute(["id" => $currentId]);

    return $stmt->fetchAll();
}