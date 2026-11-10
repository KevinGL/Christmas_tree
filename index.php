<?php

session_start();

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

else
{
    header("location: home.php");
    die();
}