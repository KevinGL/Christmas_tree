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

function isSupportedByCurrentUser(string $ids): bool
{
    $idsArray = explode(",", decryptData($ids));
    return in_array($_SESSION["auth"], $idsArray);
}

function getSupported(): array
{
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT w.name AS gift, w.supported AS supported, u.username AS username FROM users u LEFT JOIN wish_lists w ON w.user_id = u.id WHERE u.id <> :user_id");
    $stmt->execute(["user_id" => $_SESSION["auth"]]);
    $gifts = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id != :id");
    $stmt->execute(["id" => $_SESSION["auth"]]);
    $users = $stmt->fetchAll();

    foreach($users as &$u)
    {
        $u["supported"] = false;
    }

    foreach($gifts as $g)
    {
        $supported = $g["supported"] ? decryptData($g["supported"]) : "";
        $supportedArray = $supported !== "" ? explode(",", $supported) : [];

        if(in_array($_SESSION["auth"], $supportedArray))
        {
            /*$index = array_find_key($users, function ($u) use ($g)
            {
                return $u["username"] === $g["username"];
            });*/

            $index = -1;

            for($i = 0 ; $i < count($users) ; $i++)
            {
                if($users[$i]["username"] === $g["username"])
                {
                    $index = $i;
                    break;
                }
            }

            if($index > -1)
            {
                $users[$index]["supported"] = true;
            }
        }
    }

    return $users;
}