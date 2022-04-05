<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class FormPost extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'form_post');
    }

    /**
     * get form controls for a form
     * @param int $id_form_
     * @return array
     */
    function createFormPost(int $id_form_, array $data_): int
    {
        $this->reset();
        $this->copyfrom([
            'id_form' => $id_form_,
            'data' => json_encode($data_)
        ]);
        $this->insert();
        return (int)$this->get('_id') ?: 0;
    }

    /**
     * get form data by form id
     * @param int $id_form_
     * @return array
     */
    function getFormPostsByFormId(int $id_form_): array
    {
        $_result = [];
        foreach ($this->find(['id_form = ?', $id_form_]) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }
}
