<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Controller;

use WildtierSchweiz\WtFormWeb\Application;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormControl;
use WildtierSchweiz\WtFormWeb\Model\FormPost;

final class forms extends Application
{
    /**
     * @var array 
     * form configuration
     */
    private array $_form;

    /**
     * GET requests
     */
    function get()
    {
        if (!$this->getForm()) {
            self::$_f3->error(404);
            return;
        }
        self::$_f3->set('VIEWVARS.form', $this->_form);
    }

    /**
     * POST requests
     */
    function post()
    {
        if (!$this->getForm()) {
            self::$_f3->error(404);
            return;
        }
        if ($this->validateForm() === true) {
            $_form_post = new FormPost();
            $_form_post->createFormPost($this->_form['id'], self::$_f3->get('POST'));
        }
        self::$_f3->set('VIEWVARS.form', $this->_form);
    }

    /**
     * get form and controls
     * @return bool false: form not found / true: form loaded
     */
    private function getForm(): bool
    {
        $_form = new Form();
        if (!($_form_slug = self::$_f3->get('PARAMS.form')))
            return false;
        $_form_rec = $_form->getFormBySlug($_form_slug);
        if (!count($_form_rec))
            return false;
        $this->_form = $_form_rec[0];
        $_form_control = new FormControl();
        $_form_control_rec = $_form_control->getFormControlsByFormId($this->_form['id']);
        if (!count($_form_control_rec))
            return false;
        $this->_form['_controls'] = $_form_control_rec;
        return true;
    }

    /**
     * server side form validation
     * @return bool
     */
    private function validateForm(): bool
    {
        $_result = true;
        foreach ($this->_form['_controls'] as $_key => $_ctrl) {
            if ($_ctrl['is_required'] && !$_POST[$_ctrl['name']]) {
                $this->_form['_controls'][$_key]['_class'] = 'is-invalid';
                $_result = false;
            } else {
                $this->_form['_controls'][$_key]['_class'] = 'is-valid';
            }   
        }
        return $_result;
    }
}
