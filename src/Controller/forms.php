<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use Exception;
use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Service\FormService;
use WildtierSchweiz\WtFormWeb\Service\MailService;

final class forms extends Application
{
    /**
     * GET requests
     */
    function get()
    {
        $_form_service = FormService::instance();
        $_form_slug = self::$_f3->get('PARAMS.form');
        $_form_lang = self::$_f3->get('PARAMS.lang');
        if (!$_form_slug) {
            self::$_f3->error(404);
            return;
        }
        try {
            $_form_service->loadForm($_form_slug, $_form_lang);
            self::$_f3->set('VIEWVARS.form', $_form_service->getForm());
        } catch (Exception $e_) {
            self::$_f3->error(500, $e_->getMessage());
        }
    }

    /**
     * POST requests
     */
    function post()
    {
        $_form_service = FormService::instance();
        $_form_slug = self::$_f3->get('PARAMS.form');
        $_form_lang = self::$_f3->get('PARAMS.lang');
        if (!$_form_slug) {
            self::$_f3->error(404);
            return;
        }
        try {
            $_form_service->loadForm($_form_slug, $_form_lang);
            $_form = $_form_service->getForm();
            if ($_form_service->validateForm() === true) {
                if ($_form_service->postForm(self::$_f3->get('POST')) === true) {
                    $_mail_service = MailService::instance();
                    $_mail_service->sendMail(
                        [$_form['recv_mail'] => $_form['recv_name']],
                        'Wildtier Schweiz (Forms) - ' . $_form['label'],
                        'test'
                    );
                }
            }
            self::$_f3->set('VIEWVARS.form', $_form);
        } catch (Exception $e_) {
            self::$_f3->error(500, $e_->getMessage());
        }
    }
}
