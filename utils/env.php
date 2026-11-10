<?php

function getEnvVar()
{
    $res = [];

    $content = file_get_contents(__DIR__ . "/../.env");
    $vars = explode("\n", $content);

    foreach($vars as $var)
    {
        [$key, $value] = explode("=", $var);

        if($value[0] === "\"")
        {
            $value = substr($value, 1);
        }

        $posQuote = strrpos($value, "\"");

        if($posQuote > 0)
        {
            $value = substr($value, 0, $posQuote);
        }

        else
        if($posQuote === 0)
        {
            $value = "";
        }

        $res[$key] = $value;
    }

    return $res;
}