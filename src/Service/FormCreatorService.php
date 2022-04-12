<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Service;

use Base;
use Log;
use Prefab;

/**
 * create form session based
 */
class FormCreatorService extends Prefab
{
    /**
     * @var Base
     * form configuration
     */
    static private Base $_f3;

    function __construct()
    {
        self::$_f3 = Base::instance();

        //self::$_f3->set('SESSION._creator.form', []);
        //self::$_f3->set('SESSION._creator.form_text', []);

        self::addForm();

        
        //self::addFormText();
        //self::addFormText();
    }

    static public function addForm()
    {
        if (!count(self::$_f3->get('SESSION._creator.form') ?? [])) {
            self::$_f3->set('SESSION._creator.form', []);
            self::$_f3->set('SESSION._creator.form', [
                'slug' => [
                    'id' => 'slug',
                    'type' => 'text',
                    'label' => 'Slug',
                    'value' => '',
                ],
                'recv_name' => [
                    'id' => 'recv_name',
                    'type' => 'text',
                    'label' => 'Receiver Name',
                    'value' => '',
                ],
                'recv_mail' => [
                    'id' => 'recv_mail',
                    'type' => 'email',
                    'label' => 'Receiver Mail',
                    'value' => '',
                ],
            ]);
        }
    }


    static public function addFormText()
    {
        self::$_f3->push('SESSION._creator.form_text', [
            [
                'id' => 'lang',
                'type' => 'select',
                'label' => 'Language',
                'options' => self::getLanguageList(),
                'value' => '',
            ],
            [
                'id' => 'label',
                'type' => 'text',
                'label' => 'Label',
                'value' => '',
            ],
            [
                'id' => 'label_submit',
                'type' => 'text',
                'label' => 'Submit Label',
                'value' => '',
            ],
            [
                'id' => 'description',
                'type' => 'textarea',
                'label' => 'Submit Label',
                'value' => '',
            ],
            [
                'id' => 'feedback_valid',
                'type' => 'text',
                'label' => 'Feedback Valid',
                'value' => '',
            ],
            [
                'id' => 'feedback_invalid',
                'type' => 'text',
                'label' => 'Feedback Invalid',
                'value' => '',
            ],
        ]);
    }

    static public function addFormControl()
    {
    }

    static public function addFormControlText()
    {
    }

    static public function getForm(): array
    {
        return self::$_f3->get('SESSION._creator.form');
    }

    static public function getFormText(): array
    {
        return self::$_f3->get('SESSION._creator.form_text');
    }

    static private function getLanguageList(): array
    {
        $_result = [];
        foreach (glob('../dict/*.ini') as $_file) {
            $_lang = explode('.', end(explode('/', $_file)))[0];
            $_result[$_lang] = strtoupper($_lang);
        }
        return $_result;
    }

    static public function setValues()
    {
        foreach (self::$_f3->get('POST.form') as $_key => $_value) {
            self::$_f3->set('SESSION._creator.form.' . $_key . '.value', $_value);
        }
        self::logs(self::$_f3->get('SESSION._creator.form'));
    }

    static private function logs(mixed $subject_)
    {
        $_log = new Log('log.txt');
        $_log->write(print_r($subject_, true));
    }
}
