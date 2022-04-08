<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class FormControl extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'form_control');
    }

    /**
     * get form controls for a form
     * @param int $id_form_
     * @return array
     */
    function getFormControlsByFormId(int $id_form_): array
    {
        $_result = [];
        foreach ($this->find(['id_form = ?', $id_form_], ['order' => 'sortnr ASC']) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }

    /**
     * get csv header 
     */
    function getCsvHeaderByFormId(int $id_form_, string $delimiter_ = ';'): string
    {
        $_result = [];
        foreach ($this->find(['id_form = ?', $id_form_], ['order' => 'sortnr ASC']) as $_r)
            $_result[] = $_r['name'];
        return implode($delimiter_, $_result);
    }
}
