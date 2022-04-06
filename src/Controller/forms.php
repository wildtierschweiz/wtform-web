<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormControl;
use WildtierSchweiz\WtFormWeb\Model\FormPost;

final class forms extends Application
{
    private int $_form_id;

    function get()
    {
        $this->getForm();
    }

    /**
     * form posts
     */
    function post()
    {
        $this->getForm();
        if ($this->validateForm() === true) {
            $_form_post = new FormPost();
            $_form_post->createFormPost($this->_form_id, self::$_f3->get('POST'));
        }
    }

    private function getForm()
    {
        $_form = new Form();
        if (!($_form_slug = self::$_f3->get('PARAMS.form'))) {
            self::$_f3->error(404);
            return;
        }
        $_form_rec = $_form->getFormBySlug($_form_slug);
        if (!count($_form_rec)) {
            self::$_f3->error(404);
            return;
        }
        self::$_f3->set('VIEWVARS.form', $_form_rec[0]);
        $this->_form_id = $_form_rec[0]['id'];
        $_form_control = new FormControl();
        $_form_control_rec = $_form_control->getFormControlsByFormId($this->_form_id);
        if (!count($_form_control_rec)) {
            self::$_f3->error(404);
            return;
        }
        self::$_f3->set('VIEWVARS.form._controls', $_form_control_rec);
    }

    private function validateForm(): bool
    {
        $_result = true;

        foreach (self::$_f3->get('VIEWVARS.form._controls') as $_key => $_ctrl) {
            if ($_ctrl['is_required'] && !$_POST[$_ctrl['name']])
                self::$_f3->set('VIEWVARS.form._controls.' . $_key . '._class', 'is-invalid');
        }

        return $_result;
    }
}
