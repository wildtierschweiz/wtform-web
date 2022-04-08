<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Service;

use Exception;
use Prefab;
use WildtierSchweiz\WtFormWeb\Model\Form;
use WildtierSchweiz\WtFormWeb\Model\FormControl;
use WildtierSchweiz\WtFormWeb\Model\FormControlText;
use WildtierSchweiz\WtFormWeb\Model\FormPost;

class FormService extends Prefab
{
    /**
     * @var array
     * form configuration
     */
    static private array $_form;

    function __construct()
    {
    }

    /**
     * get complete form config
     * @param string $form_slug_
     * @param string $lang_
     * @throws FORM_NOT_FOUND
     * @throws NO_FORM_CONTROLS_DEFINED
     * @return array
     */
    static function loadForm(string $form_slug_, string $lang_): void
    {
        $_form = new Form();
        $_form_rec = $_form->getFormBySlug($form_slug_);
        if (!count($_form_rec))
            throw new Exception('FORM_NOT_FOUND');
        self::$_form = $_form_rec[0];
        $_form_control = new FormControl();
        $_form_control_rec = $_form_control->getFormControlsByFormId(self::$_form['id']);
        if (!count($_form_control_rec))
            throw new Exception('NO_FORM_CONTROLS_DEFINED');
        $_form_control_text = new FormControlText();
        foreach ($_form_control_rec as $_key => $_value) {
            $_form_control_text_rec = $_form_control_text->getFormControlTexts($_value['id'], $lang_);
            $_form_control_rec[$_key] = array_merge(
                $_form_control_rec[$_key],
                [
                    'label' => '',
                    'description' => NULL,
                    'feedback_valid' => NULL,
                    'feedback_invalid' => NULL,
                    'options' => NULL,
                ],
                $_form_control_text_rec[0]
            );
        }
        self::$_form['_controls'] = $_form_control_rec;
    }

    /**
     * server side form validation
     * @return bool
     */
    static function validateForm(): bool
    {
        $_result = true;
        foreach (self::$_form['_controls'] as $_key => $_ctrl) {
            if ($_ctrl['is_required'] && !($_POST[$_ctrl['name']] ?? '')) {
                self::$_form['_controls'][$_key]['_class'] = 'is-invalid';
                $_result = false;
            } else {
                self::$_form['_controls'][$_key]['_class'] = 'is-valid';
            }
        }
        self::$_form['_valid'] = $_result;
        return $_result;
    }

    /**
     * save the post
     * @param array $data_
     * @throws FORM_NOT_SAVED
     */
    static function postForm(array $data_)
    {
        $_form_post = new FormPost();
        if (!$_form_post->createFormPost(self::$_form['id'], $data_))
            throw new Exception('FORM_NOT_SAVED');
    }

    /**
     * get form csv data
     * @param string $form_slug_
     * @throws FORM_NOT_FOUND
     * @return string
     */
    static function getFormDataCsv(string $form_slug_): string
    {
        $_result = '';
        $_form = new Form();
        $_form_rec = $_form->getFormBySlug($form_slug_);
        if (!count($_form_rec))
            throw new Exception('FORM_NOT_FOUND');
        $_form_post = new FormPost();
        $_form_control = new FormControl();
        $_header_csv = $_form_control->getCsvHeaderByFormId($_form_rec[0]['id']);
        $_form_post_rec = $_form_post->getFormPostsByFormId($_form_rec[0]['id']);
        // output bom for excel compatible csv
        $_result .= chr(0xEF) . chr(0xBB) . chr(0xBF);
        $_result .= $_header_csv . "\n";
        foreach ($_form_post_rec as $_r) {
            // remove line breaks from data
            $_data = json_decode($_r['data'], true);
            array_walk($_data, function(&$item_, $index_) {
                $item_ = str_replace("\r\n", " ", $item_);
            });
            $_result .= implode(';', $_data) . "\n";
        }
        return $_result;
    }

    /**
     * get loaded form config
     * @return array
     */
    static function getForm(): array
    {
        return self::$_form;
    }
}
