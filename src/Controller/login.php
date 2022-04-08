<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use Exception;
use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\AuthService;

final class login extends Application
{
    function get()
    {
        $_auth_service = AuthService::instance();
        if ($_auth_service->isAuthenticated()) {
            self::$_f3->reroute('/' . self::$_f3->get('PARAMS.lang') . '/data');
            return;
        }
    }

    function post()
    {
        $_auth_service = AuthService::instance();
        try {
            $_auth_service->login(self::$_f3->get('POST.email'), self::$_f3->get('POST.password'));
            self::$_f3->reroute('/' . self::$_f3->get('PARAMS.lang') . '/data');
        } catch (Exception $e_) {
            self::$_f3->error(403, $e_->getMessage());
        }
    }
}
