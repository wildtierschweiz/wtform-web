<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormControl;

final class forms extends Application
{
    function get()
    {
        $_form = new Form();
        
        if (!($_form_name = self::$_f3->get('PARAMS.form'))) {
            self::$_f3->error(404);
            return;
        }

        $_form_rec = $_form->getFormByName($_form_name);
        if (!count($_form_rec)) {
            self::$_f3->error(404);
            return;
        }

        self::$_f3->set('VIEWVARS.form', $_form_rec[0]);

        $_form_id = $_form_rec[0]['id'];
        $_form_control = new FormControl();
        $_form_control_rec = $_form_control->getFormControlsByFormId($_form_id);
        if (!count($_form_control_rec)) {
            self::$_f3->error(404);
            return;
        }

        self::$_f3->set('VIEWVARS.form._controls', $_form_control_rec);
    }
}
