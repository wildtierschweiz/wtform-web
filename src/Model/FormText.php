<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class FormText extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'form_text');
    }

    /**
     * get form text
     * @param int $id_form_
     * @param string $lang_
     * @return array
     */
    function getFormTexts(int $id_form_, string $lang_): array
    {
        $_result = [];
        foreach ($this->find(['id_form = ? AND lang = ?', $id_form_, $lang_]) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }
}
