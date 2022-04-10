<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;

final class create extends Application
{
    function get()
    {
        $_form = new Form();

        self::$_f3->set('VIEWVARS.formlist', $_form->getForms());
    }
}
