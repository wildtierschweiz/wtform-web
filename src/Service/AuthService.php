<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Service;

use Exception;
use Prefab;
use WildtierSchweiz\WtFormWeb\Model\User;

class AuthService extends Prefab
{
    function __construct()
    {
    }

    /**
     * perform a login
     * @param string $email_
     * @param string $password_
     * @return bool
     */
    static function login(string $email_, string $password_): bool
    {
        $_user = new User();
        $_user_rec = $_user->getUserByEmailAddress($email_);
        if (!count($_user_rec) || !password_verify($password_, $_user_rec[0]['password']))
            return false;
        self::setUserId($_user_rec[0]['email']);
        return true;
    }

    /**
     * end a user session
     */
    static function logout(): void
    {
        session_start();
        session_unset();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * perform a login
     * @param string $email_
     * @param string $password_
     * @return bool
     */
    static function isAuthenticated(): bool
    {
        return self::getUserId() ? true : false;
    }

    /**
     * get the logged in user id
     * @return string
     */
    static private function getUserId(): string
    {
        return $_SESSION['user'] ?? '';
    }

    /**
     * set user id of logged in user
     * @param string $user_id_
     * @return string
     */
    static private function setUserId(string $user_id_): string
    {
        return $_SESSION['user'] = $user_id_;
    }
}
