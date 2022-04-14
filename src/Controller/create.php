<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use Log;
use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\FormCreatorService;

final class create extends Application
{
    function get()
    {
        $_form_creator_service = FormCreatorService::instance();

        if (self::$_f3->get('GET.clear') !== NULL)
            $_form_creator_service::resetForm();

        foreach (glob('../dict/*.ini') as $_file) {
            $_lang = explode('.', end(explode('/', $_file)))[0];
            self::$_f3->push('VIEWVARS.langlist', [
                'key' => $_lang,
                'value' => strtoupper($_lang)
            ]);
        }

        self::$_f3->set('VIEWVARS.form', $_form_creator_service->getForm());
        self::$_f3->set('VIEWVARS.form_text', $_form_creator_service->getFormText());
        self::$_f3->set('VIEWVARS.form_control', $_form_creator_service->getFormControl());
    }

    /**
     * POST requests
     */
    function post()
    {
        $_form_creator_service = FormCreatorService::instance();
        switch (self::$_f3->get('POST._action')) {
            case 'addFormText':
                $_form_creator_service->addFormText();
                break;
        }
        $_form_creator_service->setValues();
        self::$_f3->reroute('/' . self::$_f3->get('PARAMS.lang') . '/' . self::$_f3->get('PARAMS.page'));
    }
}
