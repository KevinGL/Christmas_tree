<?php

function reinitSession()
{
    if(session_status() === PHP_SESSION_ACTIVE)
    {
        $_SESSION = array();
        if(ini_get("session.use_cookies"))
        {
            $params = session_get_cookie_params();
            
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    ///////////////////////////////////////////////////////////

    $lifetime = 30 * 24 * 3600;

    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'domain'   => '',
        'secure'   => true,  // true si vous êtes en HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}