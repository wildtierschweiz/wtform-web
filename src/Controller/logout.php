<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\AuthService;

final class logout extends Application
{
    function get()
    {
        $_auth_service = AuthService::instance();
        $_auth_service->logout();
        self::$_f3->reroute('/' . self::$_f3->get('PARAMS.lang') . '/login');
        return;
    }
}
