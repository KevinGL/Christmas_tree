<?php

function getRandomCode(int $nbChars): string
{
    $chars = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    $res = "";

    for($i = 0 ; $i < $nbChars ; $i++)
    {
        $index = rand(0, 9);
        $res .= $chars[$index];
    }

    return $res;
}