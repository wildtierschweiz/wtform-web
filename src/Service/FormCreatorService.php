<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Service;

use Basket;
use Prefab;

/**
 * create form session based
 */
class FormCreatorService extends Prefab
{
    /**
     * @var array
     * form configuration
     */
    static private Basket $_form;

    function __construct(string $name_ = 'form')
    {
        self::$_form = new Basket($name_);
    }

    public function addFormText()
    {
    }

    public function addFormControl()
    {
    }

    public function addFormControlText()
    {
    }
}
