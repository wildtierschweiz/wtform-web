<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Service\FormService;

final class data extends Application
{
    /**
     * list forms
     */
    function get()
    {
        $_form = new Form();
        $_form_slug = self::$_f3->get('PARAMS.form');
        if (!$_form_slug) {
            self::$_f3->set('VIEWVARS.forms', $_form->getForms());
            return;
        }
        $_form_service = FormService::instance();
        self::$_f3->set('RESPONSE.data', $_form_service->getFormDataCsv($_form_slug));
        self::$_f3->set('RESPONSE.mime', 'text/csv');
        self::$_f3->set('RESPONSE.filename', 'data-' . $_form_slug . '.csv');
    }
}
