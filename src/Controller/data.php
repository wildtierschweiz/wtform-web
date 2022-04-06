<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormControl;
use WildtierSchweiz\WtFormWeb\Model\FormPost;

final class data extends Application
{
    /**
     * list forms
     */
    function get()
    {
        $_form = new Form();
        if (!($_form_slug = self::$_f3->get('PARAMS.form'))) {
            self::$_f3->set('VIEWVARS.forms', []);
            foreach ($_form->getForms() as $_r)
                self::$_f3->push('VIEWVARS.forms', $_r);
            return;
        }
        $_form_rec = $_form->getFormBySlug($_form_slug);
        if (!count($_form_rec)) {
            self::$_f3->error(404);
            return;
        }
        $_form_post = new FormPost();
        $_form_control = new FormControl();
        $_header_csv = $_form_control->getCsvHeaderByFormId($_form_rec[0]['id']);
        $_form_post_rec = $_form_post->getFormPostsByFormId($_form_rec[0]['id']);
        self::$_f3->set('RESPONSE.filename', 'data-'.$_form_rec[0]['slug'].'.csv');
        self::$_f3->set('RESPONSE.mime', 'text/csv');
        // output bom for excel compatible csv
        self::$_f3->set('RESPONSE.data', (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        self::$_f3->set('RESPONSE.data', self::$_f3->get('RESPONSE.data') . $_header_csv . "\n");
        foreach ($_form_post_rec as $_r)
            self::$_f3->set('RESPONSE.data', self::$_f3->get('RESPONSE.data') . implode(';', json_decode(/*utf8_decode(*/$_r['data']/*)*/, true)) . "\n");
    }
}
