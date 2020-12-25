<?php
namespace workbook\wbinc\dokuadmin;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class DokuadminInfra {
    /* -------------------------------------------------------------------- */
    public static $Infras = [ //
        'Infra' => '', //
        'PHP' => '', //
        'PHP-Ioncube' => '', //
        'Webroot' => '', //
        'Dokuwiki' => '', //
    ];
    /* -------------------------------------------------------------------- */
    public static function Action($inAction, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::$Infras[$inId];
                break;
            case 'status':
                $ar = ['Infra' => 'infra-ok', 'PHP' => 'php-ok', 'PHP-Ioncube' => 'ioncube-ok', 'Dokuwiki' => 'dokuwiki-ok', 'Webroot' => 'webroot-ok'];
                $color = strpos(WB_RUNMODE, $ar[$inId]) === false ? 'red' : 'green';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            case 'info':
                switch ($inId) {
                    case 'Infra':
                        $return .= 'INFRA -----------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $return .= self::__LineGet('UNAME', php_uname());
                        $return .= self::__LineGet('ARCH', php_uname('m'));
                        $return .= self::__LineGet('OS', php_uname('s')); // PHP_OS
                        $return .= self::__LineGet('HOSTNAME', gethostname()); // php_uname('n')
                        $return .= self::__LineGet('HOSTNAMECTL', shell_exec('hostnamectl'));
                        $return .= 'DISK -----------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $return .= self::__LineGet('FREE-SPACE', @round(disk_free_space('.') / 10 ** 9) . 'GB');
                        $return .= self::__LineGet('LOCALE', setlocale(LC_ALL, 0));
                        $return .= 'PHP ------------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $return .= self::__LineGet('PHP-SAPI', php_sapi_name());
                        $return .= self::__LineGet('PHP-VERSION', phpversion());
                        $return .= self::__LineGet('PHP-EXTENSIONS', implode(', ', get_loaded_extensions()));
                        $return .= self::__LineGet('PHP-TIMEZONE', date_default_timezone_get() . " (now: " . date('Y-m-d-His') . ")");
                        $return .= self::__LineGet('PHP-CWD', getcwd());
                        $return .= 'PHP.INI --------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $return .= self::__LineGet('PHP.INI', php_ini_loaded_file());
                        $ar = ['memory_limit' => '', 'max_execution_time' => '', 'safe_mode' => '', 'ignore_user_abort' => '', 'disable_functions' => '', 'sendmail_path' => ''];
                        foreach ($ar as $id => $val) {
                            $return .= self::__LineGet("PHP.INI: {$id}", ini_get($id));
                        }
                        $return .= 'ACCESS ---------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $ar = [getcwd(), '/home', '/var/log/syslog', '/var/log/apache2', '/var/www'];
                        foreach ($ar as $val) {
                            $str = '';
                            if (@is_readable($val)) $str .= "readable ";
                            if (@is_writeable($val)) $str .= "writeable ";
                            if (@is_executable($val)) $str .= "executable ";
                            $return .= self::__LineGet($val, $str);
                        }
                        $return .= 'COMMANDS -------------------------------------------------------------------------------------------------------------------------------' . "\n";
                        $ar = [ //
                            'which sendmail', 'which postfix', 'which exim', //
                            'which dpkg', 'which rpm', 'which yum', //
                            'which sapp', 'which unoconv', //
                        ];
                        foreach ($ar as $val) {
                            $return .= self::__LineGet($val, trim(shell_exec($val)));
                        }
                        break;
                    case 'PHP':
                        phpinfo();
                        exit;
                        break;
                    case 'PHP-Ioncube':
                        $return .= extension_loaded('ioncube Loader') ? 'PHP-Ioncube extension loaded' : 'PHP-Ioncube extension missing';
                        break;
                    case 'Webroot':
                        $return .= strpos(WB_RUNMODE, 'webroot-ok') === false ? 'Webroot (' . getcwd() . ') is not writeable.' : 'Webroot (' . getcwd() . ') is writeable.';
                        break;
                    case 'Dokuwiki':
                        $return .= "See <a href='http://www.dokuwiki.org' target='_blank'>Dokuwiki</a>";
                        break;
                }
                break;
            case 'version':
                switch ($inId) {
                    case 'PHP':
                        $return .= PHP_VERSION;
                        break;
                    case 'Dokuwiki':
                        $return .= doku\DokuConf::VersionGet();
                        break;
                }
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', '', '', "Infra action unknown: $inAction $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __LineGet($inId, $inValues) {
        $return = '';
        $ar = explode("\n", $inValues);
        $return .= '- ' . str_pad($inId, 30, '.') . ": " . array_shift($ar) . "\n";
        foreach ($ar as $val) {
            if (empty($val)) continue;
            $return .= '- ' . str_pad('', 30, '.') . ": $val" . "\n";
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}