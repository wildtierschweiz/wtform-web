<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\FormService;

final class data extends Application
{
    /**
     * list forms
     */
    function get()
    {
        $_form_service = FormService::instance();
        $_form_slug = self::$_f3->get('PARAMS.form');
        $_form_lang = self::$_f3->get('PARAMS.lang');
        if (!$_form_slug) {
            self::$_f3->set('VIEWVARS.forms', $_form_service->getFormList($_form_lang));
            return;
        }
        self::$_f3->set('RESPONSE.data', $_form_service->getFormDataCsv($_form_slug));
        self::$_f3->set('RESPONSE.mime', 'text/csv');
        self::$_f3->set('RESPONSE.filename', 'data-' . $_form_slug . '.csv');
    }
}
