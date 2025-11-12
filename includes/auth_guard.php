<?php

declare(strict_types=1);

if (!function_exists('require_login')) {
    /**
     * Redirects anonymous visitors to the login page and ensures the session is active.
     */
    function require_login(string $redirectTo = 'login.php'): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }
}
