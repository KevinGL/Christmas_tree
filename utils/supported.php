<?php

require_once __DIR__ . "/../db/pdo.php";
require_once __DIR__ . "/../safety/encrypt.php";

function getUsernamesFromIds(string $ids): string
{
    $idsArray = explode(",", $ids);
    $res = "";
    $pdo = getPDO();

    for($i = 0 ; $i < count($idsArray) ; $i++)
    {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
        $stmt->execute(["id" => $idsArray[$i]]);

        $username = $stmt->fetch()["username"];

        $res .= $username;

        if($i < count($idsArray) - 1)
        {
            $res .= ", ";
        }
    }

    return $res;
}

function getUsernamesArrayFromIds(array $ids): array
{
    $res = [];
    $pdo = getPDO();

    for($i = 0 ; $i < count($ids) ; $i++)
    {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
        $stmt->execute(["id" => $ids[$i]]);

        $username = $stmt->fetch()["username"];

        $res[] = $username;
    }

    return $res;
}

function isSupportedByCurrentUser(string $ids): bool
{
    $idsArray = explode(",", decryptData($ids));
    return in_array($_SESSION["auth"], $idsArray);
}

function getSupported(): array
{
    $pdo = getPDO();

    $stmt = $pdo->query("SELECT supported, user_id FROM wish_lists");
    $wishs = $stmt->fetchAll();

    $wishs = array_filter($wishs, function ($w)
    {
        $users = explode(",", decryptData($w["supported"]));
        return in_array($_SESSION["auth"], $users);
    });

    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != :id ORDER BY username");
    $stmt->execute(["id" => $_SESSION["auth"]]);
    $users = $stmt->fetchAll();

    $usersById = [];

    foreach($users as $user)
    {
        $key = $user["id"];
        $usersById[$key] = ["username" => $user["username"], "supported" => false];
    }

    foreach($wishs as $w)
    {
        $ids = explode(",", $w["user_id"]);

        foreach($ids as $id)
        {
            $usersById[$id]["supported"] = true;
        }
    }

    return $usersById;
}