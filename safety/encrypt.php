<?php

require_once __DIR__ . "/../utils/env.php";

function encryptData(string $plainText): string
{
    $key = getEnvVar()["ENCRYPTION_KEY"];
    $method = getEnvVar()["ENCRYPTION_METHOD"];

    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    
    $encrypted = openssl_encrypt($plainText, $method, $key, 0, $iv);
    
    return base64_encode($iv . $encrypted);
}

function decryptData(string $cipherText): string
{
    $key = getEnvVar()["ENCRYPTION_KEY"];
    $method = getEnvVar()["ENCRYPTION_METHOD"];

    $data = base64_decode($cipherText);
    $ivLength = openssl_cipher_iv_length($method);
    
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}