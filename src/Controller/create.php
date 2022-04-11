<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use Log;
use WildtierSchweiz\WtFormWeb\Application;

final class create extends Application
{
    function get()
    {
        foreach (glob('../dict/*.ini') as $_file) {

            $_lang = explode('.', end(explode('/', $_file)))[0];
            //$_language = end

            $_log = new Log('log.txt');
            $_log->write($_lang);

            self::$_f3->push('VIEWVARS.langlist', [
                'key' => $_lang,
                'value' => strtoupper($_lang)
            ]);
        }
    }
}
