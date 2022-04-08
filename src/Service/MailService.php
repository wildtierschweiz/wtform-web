<?php

declare(strict_types=1);

namespace WildtierSchweiz\WtFormWeb\Service;

use Base;
use Exception;
use Prefab;
use SMTP;
use WildtierSchweiz\WtFormWeb\Application;

class MailService extends Prefab
{
    /**
     * @var SMTP
     * smtp connection
     */
    private static SMTP $_smtp;

    /**
     * @var Base
     * f3 framework instance
     */
    private static Base $_f3;

    function __construct()
    {
        self::$_smtp = Application::instance()::getMail();
        self::$_f3 = Application::instance()::getF3();
    }

    /**
     * perform a login
     * @param array $to_
     * @param string $subject_
     * @param string $message_
     * @param array $from_
     * @param array $attach_
     * @param string $charset_
     * @return bool
     */
    static function sendMail(array $to_, string $subject_, string $message_, array $from_ = [], array $attach_ = [], string $charset_ = ''): bool
    {
        $_charset = $charset_ ?: self::$_f3->get('ENCODING');
        $_toaddr = [];
        foreach ($to_ as $email_ => $name_)
            $_toaddr[] = ($name_ ? '"' . $name_ . '"' : '') . ' <' . $email_ . '>';
        $_toaddr = implode(', ', $_toaddr);
        $_fromaddr = [];
        foreach ($from_ as $email_ => $name_)
            $_fromaddr[] = ($name_ ? '"' . $name_ . '"' : '') . ' <' . $email_ . '>';
        if (!count($_fromaddr))
            $_fromaddr[] = ((self::$_f3->get('mail.defaultsender.name') ?? '') ? '"' . self::$_f3->get('mail.defaultsender.name') . '"' : '') . ' <' . self::$_f3->get('mail.defaultsender.email') . '>';
        $_fromaddr = $_fromaddr[0];
        foreach ($attach_ as $_attachment)
            self::$_smtp->attach($_attachment);
        self::$_smtp->set('Content-type', self::$_f3->get('mail.mime') . '; charset=' . $_charset);
        self::$_smtp->set('To', $_toaddr);
        self::$_smtp->set('From', $_fromaddr);
        self::$_smtp->set('Subject', $subject_);
        return self::$_smtp->send($message_);
    }
}
