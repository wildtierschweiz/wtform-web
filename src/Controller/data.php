<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormPost;

final class data extends Application
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

        $_form_post = new FormPost();

        $_form_post_rec = $_form_post->getFormPostsByFormId($_form_id);

        foreach ($_form_post_rec as $_r) {
            self::$_f3->set('RESPONSE.filename', 'data.csv');
            self::$_f3->set('RESPONSE.mime', 'text/csv');
            self::$_f3->set('RESPONSE.data', implode(';', json_decode($_r['data'], true)) . "\n");
        }
    }
}
