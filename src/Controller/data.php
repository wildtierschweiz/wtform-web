<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use Exception;
use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\AuthService;
use WildtierSchweiz\WtFormWeb\Service\FormService;

final class data extends Application
{
    /**
     * list forms
     */
    function get()
    {
        $_auth_service = AuthService::instance();
        if (!$_auth_service->isAuthenticated()) {
            self::$_f3->reroute('/' . self::$_f3->get('PARAMS.lang') . '/login');
            return;
        }
        $_form_service = FormService::instance();
        $_form_slug = self::$_f3->get('PARAMS.form');
        $_form_lang = self::$_f3->get('PARAMS.lang');
        if (!$_form_slug) {
            $_form_rec = $_form_service->getFormList($_form_lang);
            foreach ($_form_rec as $_key => $_value) {
                self::$_f3->set('VIEWVARS.forms.'.$_key, $_value);
                self::$_f3->set('VIEWVARS.forms.'.$_key.'._posts', $_form_service->getFormPostList($_value['slug']));
            }
            return;
        }
        try {
            self::$_f3->set('RESPONSE.data', $_form_service->getFormDataCsv($_form_slug));
            self::$_f3->set('RESPONSE.mime', 'text/csv');
            self::$_f3->set('RESPONSE.filename', 'data-' . $_form_slug . '.csv');
        } catch (Exception $e_) {
            self::$_f3->error(500, $e_->getMessage());
        }
    }
}
