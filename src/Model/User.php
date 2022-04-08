<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Model;

use WildtierSchweiz\WtFormWeb\Application;
use DB\SQL\Mapper;

class User extends Mapper
{
    function __construct()
    {
        parent::__construct(Application::instance()::getDB(), 'user');
    }

    /**
     * get user by email address
     * @param string $form_name_
     * @return array
     */
    function getUserByEmailAddress(string $email_): array
    {
        $_result = [];
        foreach ($this->find(['email = ?', $email_]) as $_r)
            $_result[] = $_r->cast();
        return $_result;
    }
}
