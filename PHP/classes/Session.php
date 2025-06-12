<?php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        // Supprimer toutes les variables de session
        $_SESSION = array();
        // Si un cookie de session existe, le supprimer
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return isset($_SESSION['admin_id']);
    }

    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }

    public static function getUserName() {
        return $_SESSION['user_name'] ?? null;
    }

    public static function getAdminName() {
        return $_SESSION['admin_name'] ?? null;
    }
} 