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

    $stmt = $pdo->query("SELECT id as wish_id, name as wish_name, supported as wish_supported, user_id FROM wish_lists");
    $wishs = $stmt->fetchAll();

    $usersByIds = [];
    $stmt = $pdo->query("SELECT id, username FROM users");
    $users = $stmt->fetchAll();

    foreach($users as $user)
    {
        $key = $user["id"];
        $usersByIds[$key] = $user["username"];
    }

    $wishs = array_filter($wishs, function ($item) use ($currentId)
    {
        $list = explode(",", $item["user_id"]);
        return !in_array($currentId, $list);
    });

    foreach($wishs as &$wish)
    {
        $usersList = explode(",", $wish["user_id"]);
        $wish["username"] = "";

        for($i = 0 ; $i < count($usersList) ; $i++)
        {
            $key = $usersList[$i];
            $wish["username"] .= $usersByIds[$key];

            if($i < count($usersList) - 1)
            {
                $wish["username"] .= ", ";
            }
        }
    }

    return $wishs;
}

function getOwnList(): array
{
    $currentId = $_SESSION["auth"];

    $pdo = getPDO();

    $stmt = $pdo->query("SELECT id, name, user_id FROM wish_lists");
    $wishs = $stmt->fetchAll();

    $wishs = array_filter($wishs, function ($item) use ($currentId)
    {
        $list = explode(",", $item["user_id"]);

        return in_array($currentId, $list);
    });

    return $wishs;
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
        $stmt = $pdo->query("SELECT id, name, supported, user_id FROM wish_lists");
        $wishs = $stmt->fetchAll();

        if($wishs)
        {
            $wishs = array_filter($wishs, function ($w) use ($u)
            {
                $users = explode(",", $w["user_id"]);
                return in_array($u["id"], $users);
            });
        
            foreach($wishs as $w)
            {
                $res[] = [...$w, "supported_user" => $u["id"]];
            }
        }
    }

    return $res;
}