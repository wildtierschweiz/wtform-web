<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class FormControlText extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'form_control_text');
    }

    /**
     * get form controls for a form
     * @param int $id_form_
     * @return array
     */
    function getFormControlTexts(int $id_form_control_, string $lang_): array
    {
        $_result = [];
        foreach ($this->find(['id_form_control = ? AND lang = ?', $id_form_control_, $lang_]) as $_r) {
            $_controls = $_r->cast();
            $_controls['options'] = json_decode($_controls['options'] ?? '', true);
            $_result[] = $_controls;
        }
        return $_result;
    }
}
