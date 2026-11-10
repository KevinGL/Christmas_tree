<?php

function hidePhoneNumber(string $number): string
{
    $res = "";

    for($i = 0 ; $i < strlen($number) ; $i++)
    {
        if($i < strlen($number) - 3)
        {
            $res .= "*";
        }

        else
        {
            $res .= $number[$i];
        }
    }

    return $res;
}