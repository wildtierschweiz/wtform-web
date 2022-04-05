<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb;

use Prefab;
use Base;
use Template;
use SMTP;
use DB\SQL;
use Exception;

/**
 * application base controller
 */
class Application extends Prefab
{
    // objects
    static protected Base $_f3;
    static protected SQL $_db;
    static protected SMTP $_mail;
    // constants
    private const DEFAULT_PAGE = 'home';
    private const DB_DEFAULT_TYPE = 'mysql';
    private const DB_DEFAULT_HOST = 'localhost';
    private const DB_DEFAULT_PORT = 3306;
    private const SMTP_DEFAULT_HOST = 'localhost';
    private const SMTP_DEFAULT_PORT = 25;

    /**
     * application constructor
     */
    function __construct()
    {
        self::$_f3 = Base::instance();
        foreach (glob('../config/*.ini') as $_config)
            self::$_f3->config($_config);
        self::$_db = new SQL(
            (self::$_f3->get('database.type') ?: self::DB_DEFAULT_TYPE)
                . ':host=' . (self::$_f3->get('database.host') ?: self::DB_DEFAULT_HOST)
                . ';port=' . (self::$_f3->get('database.port') ?: self::DB_DEFAULT_PORT)
                . ';dbname=' . self::$_f3->get('database.data'),
            self::$_f3->get('database.user'),
            self::$_f3->get('database.pass')
        );
        self::$_mail = new SMTP(
            self::$_f3->get('mail.host') ?: self::SMTP_DEFAULT_HOST,
            self::$_f3->get('mail.port') ?: self::SMTP_DEFAULT_PORT,
            self::$_f3->get('mail.scheme'),
            self::$_f3->get('mail.user'),
            self::$_f3->get('mail.pass')
        );
        // parse put variables and store to f3 hive PUT variable
        parse_str(file_get_contents("php://input"), $_put_vars);
        self::$_f3->set('PUT', $_put_vars);
    }

    /**
     * pre routing handler
     * @param Base $f3_ f3 framework instance
     * @return void
     */
    static function beforeroute(Base $f3_): void
    {
        // when no language files are found
        if (!count(glob($f3_->get('LOCALES') . '*.ini')))
            throw new Exception('DICTIONARY check failed');
        // detect page
        if (!$f3_->get('PARAMS.page')) {
            // try to determine page id
            $f3_->set('PARAMS.page', self::DEFAULT_PAGE);
            // update full uri parameter
            $f3_->set('PARAMS.0', '/' . $f3_->get('PARAMS.page'));
        }
        // detect language, if not set or a dictionary is not present for the current language
        if (!$f3_->get('PARAMS.lang') || !file_exists($f3_->get('LOCALES') . $f3_->get('PARAMS.lang') . '.ini')) {
            // set fallback default language
            $f3_->set('PARAMS.lang', $f3_->get('FALLBACK'));
            // try to overwrite default with brower setting auto detection
            foreach (explode(',', strtolower($f3_->get('LANGUAGE'))) as $lang) {
                // if a language file for the language exists
                if (file_exists($f3_->get('LOCALES') . $lang . '.ini')) {
                    // set the first language found to parameter
                    $f3_->set('PARAMS.lang', $lang);
                    // leave foreach loop
                    break;
                }
            }
            // reroute to seo uri
            $f3_->reroute('/' . $f3_->get('PARAMS.lang') . $f3_->get('PARAMS.0') . ($f3_->get('QUERY') ? '?' . $f3_->get('QUERY') : ''));
        }
        // set language to the one detected above
        $f3_->set('LANGUAGE', $f3_->get('PARAMS.lang'));
        // remove the language parameter
        $_t = array_filter(explode('/', $f3_->get('PARAMS.0')));
        array_shift($_t);
        $f3_->set('PARAMS.1', '/' . implode('/', $_t));
    }

    /**
     * post routing handler
     * @param Base $f3_ f3 framework instance
     * @return void
     */
    static function afterroute(Base $f3_): void
    {
        // don't include after route methods for CLI execution
        if ($f3_->get('CLI'))
            return;
        // set content type header
        header('Content-Type: ' . strtolower($f3_->get('RESPONSE.mime') ?: 'text/html'));
        // if a response filename is set
        if ($f3_->get('RESPONSE.filename'))
            // add content disposition header for downloading file
            header('Content-Disposition: attachment; filename="' . $f3_->get('RESPONSE.filename') . '"');
        // Render template depending on result mime type
        switch (strtolower($f3_->get('RESPONSE.mime') ?: 'text/html')) {
            case 'text/html':
                echo Template::instance()->render('template.htm');
                break;
            case 'application/json':
                echo json_encode($f3_->get('RESPONSE.data'), ($f3_->get('DEBUG') ? JSON_PRETTY_PRINT : 0));
                break;
            default:
                echo $f3_->get('RESPONSE.data');
                break;
        }
        // reset session based flash messages
        $f3_->set('SESSION.message', []);
    }

    /**
     * custom f3 framework error handler
     * @param Base $f3_ f3 framework
     * @return bool true error handled, false fallback to f3 error handler
     */
    static function onerror(Base $f3_): bool
    {
        if ($f3_->get('CLI') || $f3_->get('DEBUG'))
            return false;
        if ($f3_->get('AJAX'))
            return true;
        $f3_->set('PARAMS.page', '_error');
        return true;
    }

    /**
     * get database instance
     * @return SQL
     */
    static function getDB(): SQL
    {
        return self::$_db;
    }

    /**
     * get mail service
     * @return SMTP
     */
    static function getMail(): SMTP
    {
        return self::$_mail;
    }

    /**
     * run the application
     * @return void
     */
    static function run(): void
    {
        self::$_f3->run();
    }
}
