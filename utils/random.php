<?php

function getRandomCode(int $nbChars): string
{
    $res = "";

    for($i = 0 ; $i < $nbChars ; $i++)
    {
        $res .= rand(0, 9);
    }

    return $res;
}