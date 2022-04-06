<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class Form extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'form');
    }

    /**
     * get form by its name
     * @param string $form_name_
     * @return array
     */
    function getFormBySlug(string $form_name_): array
    {
        $_result = [];
        foreach ($this->find(['slug = ?', $form_name_]) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }

    /**
     * get all forms
     * @return array
     */
    function getForms(): array
    {
        $_result = [];
        foreach ($this->find(NULL, ['order' => 'name ASC']) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }
}
