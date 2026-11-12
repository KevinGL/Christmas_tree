<?php

require_once __DIR__ . "/../db/pdo.php";

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