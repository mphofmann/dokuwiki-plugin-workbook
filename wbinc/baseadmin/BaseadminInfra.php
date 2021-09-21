<?php
namespace workbook\wbinc\baseadmin;
class BaseadminInfra {
    /* -------------------------------------------------------------------- */
    public static $Infras = [ //
        'Infra' => '', //
        'PHP' => '<a href="http://www.php.net" target="_blank" class="urlextern">www.php.net</a>', //
        'PHP-Ioncube' => '<a href="http://www.ioncube.com" target="_blank" class="urlextern">www.ioncube.com</a>', //
        'Webroot' => '', //
        'Dokuwiki' => '<a href="http://www.dokuwiki.org" target="_blank" class="urlextern">www.dokuwiki.org</a>', //
        'Crontab' => '<a href="https://en.wikipedia.org/wiki/Cron" target="_blank" class="urlextern">www.wikipedia.org/wiki/Cron</a>', //
    ];
    /* -------------------------------------------------------------------- */
    public static function ConfLocalExec($inAction = 'status'): void {
        switch ($inAction) {
            case 'status':
                echo date('Y-m-d His', filemtime(WB_DATACONF . 'local.php'));
                break;
            case 'purge':
                BaseadminCache::ConfLocalTouch();
                BaseadminXhtmlMsg::Echo('Success', '', '', 'Cache purged.');
                sleep(1); // required
                break;
            case 'clear':
                BaseadminCache::ConfLocalTouch();
                BaseadminInode::Clear('data/cache/'); // TODO
                BaseadminXhtmlMsg::Echo('Success', '', '', 'All cache cleared.');
                break;
            default:
                BaseadminXhtmlMsg::EchoFalse('Notice', __METHOD__, $inAction, "Unknown action.");
                break;
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction, $inId) {
        $return = '';
        switch ($inAction) {
            case 'note':
                $return .= self::$Infras[$inId];
                break;
            case 'status':
                switch ($inId) {
                    case 'Crontab':
                        $return .= BaseadminCrontab::StatusGet();
                        break;
                    default:
                        $ar = ['Infra' => 'infra-linux', 'PHP' => 'infra-php', 'PHP-Ioncube' => 'infra-ioncube', 'Webroot' => 'infra-webroot', 'Dokuwiki' => 'controller-doku',];
                        $color = \_Wb_::RunmodeCheck($ar[$inId]) ? 'green' : 'red';
                        $return .= BaseadminXhtml::StatusGet($color);
                        break;
                }
                break;
            case 'info':
                switch ($inId) {
                    case 'Infra':
                        // Infra
                        $return .= BaseadminExec::OutputHeadingGet('INFRA');
                        $return .= BaseadminExec::OutputLinesGet('UNAME', php_uname());
                        $return .= BaseadminExec::OutputLinesGet('ARCH', php_uname('m'));
                        $return .= BaseadminExec::OutputLinesGet('OS', php_uname('s')); // PHP_OS
                        $return .= BaseadminExec::OutputLinesGet('HOSTNAME', gethostname()); // php_uname('n')
                        $return .= BaseadminExec::OutputLinesGet('HOSTNAMECTL', shell_exec('hostnamectl'));
                        // Disk
                        $return .= BaseadminExec::OutputHeadingGet('DISK');
                        $return .= BaseadminExec::OutputLinesGet('FREE-SPACE', @round(disk_free_space('.') / 10 ** 9) . 'GB');
                        $return .= BaseadminExec::OutputLinesGet('LOCALE', setlocale(LC_ALL, 0));
                        // PHP
                        $return .= BaseadminExec::OutputHeadingGet('PHP');
                        $return .= BaseadminExec::OutputLinesGet('PHP-SAPI', php_sapi_name());
                        $return .= BaseadminExec::OutputLinesGet('PHP-VERSION', phpversion());
                        $return .= BaseadminExec::OutputLinesGet('PHP-EXTENSIONS', implode(', ', get_loaded_extensions()));
                        $return .= BaseadminExec::OutputLinesGet('PHP-TIMEZONE', date_default_timezone_get() . " (now: " . date('Y-m-d-His') . ")");
                        $return .= BaseadminExec::OutputLinesGet('PHP-CWD', getcwd());
                        // php.ini
                        $return .= BaseadminExec::OutputHeadingGet('PHP.INI');
                        $return .= BaseadminExec::OutputLinesGet('PHP.INI', php_ini_loaded_file());
                        $ar = ['memory_limit' => '', 'max_execution_time' => '', 'safe_mode' => '', 'ignore_user_abort' => '', 'disable_functions' => '', 'sendmail_path' => ''];
                        foreach ($ar as $id => $val) {
                            $return .= BaseadminExec::OutputLinesGet("PHP.INI: {$id}", ini_get($id));
                        }
                        // Access
                        $return .= BaseadminExec::OutputHeadingGet('ACCESS');
                        $return .= BaseadminExec::OutputLinesGet('USER', get_current_user());
                        $return .= BaseadminExec::OutputLinesGet('ID', shell_exec('id'));
                        $ar = [getcwd(), '/home', '/var/log/syslog', '/var/log/apache2', '/var/www'];
                        foreach ($ar as $val) {
                            $str = '';
                            if (@is_readable($val)) $str .= "readable ";
                            if (@is_writeable($val)) $str .= "writeable ";
                            if (@is_executable($val)) $str .= "executable ";
                            $return .= BaseadminExec::OutputLinesGet($val, $str);
                        }
                        // Cron
                        $return .= BaseadminExec::OutputHeadingGet('CRON');
                        $return .= BaseadminExec::OutputLinesGet('CRONTAB', shell_exec('crontab -l 2>&1'));
                        // Commands
                        $return .= BaseadminExec::OutputHeadingGet('COMMANDS');
                        $ars = [ //
                            'Mails' => ['sendmail', 'postfix', 'exim'], //
                            'Convert' => ['pandoc', 'unoconv'], //
                            'Packages' => ['dpkg', 'rpm', 'yum'], //
                            'Sapp' => ['sapp'], //
                            'AWS' => ['aws'], //
                        ];
                        foreach ($ars as $id => $ar) {
                            $str = '';
                            foreach ($ar as $val) {
                                $str .= ($out = shell_exec("which $val")) == '' ? '' : "$val(" . trim($out) . ") ";
                            }
                            $return .= BaseadminExec::OutputLinesGet($id, $str);
                        }
                        // Workbooks
                        $return .= BaseadminExec::OutputHeadingGet('WORKBOOK');
                        $return .= BaseadminExec::OutputLinesGet('WB_RUNARCHLIST', WB_RUNARCHLIST);
                        $return .= BaseadminExec::OutputLinesGet('WB_RUNMODELIST', WB_RUNMODELIST);
                        break;
                    case 'PHP':
                        ob_start();
                        phpinfo();
                        $str = ob_get_clean();
                        $return .= "<pre>$str</pre>";
                        break;
                    case 'PHP-Ioncube':
                        $add = extension_loaded('ioncube Loader') ? 'PHP-Ioncube extension loaded' : 'PHP-Ioncube extension missing';
                        $return .= "<pre>$add</pre>";
                        break;
                    case 'Webroot':
                        $add = \_Wb_::RunmodeCheck('infra-webroot') ? 'Webroot (' . getcwd() . ') is writeable.' : 'Webroot (' . getcwd() . ') is not writeable.';
                        $return .= "<pre>$add</pre>";
                        break;
                    case 'Dokuwiki':
                        $add = 'Version: ' . @substr(file_get_contents('VERSION'), 0, 10) . "\n";
                        $add .= "See <a href='http://www.dokuwiki.org' target='_blank'>Dokuwiki</a>";
                        $return .= "<pre>$add</pre>";
                        break;
                    case 'Crontab':
                        $add = BaseadminCrontab::InfoGet();
                        $return .= "<pre>$add</pre>";
                        break;
                }
                break;
            case 'remove':
                switch ($inId) {
                    case 'Crontab':
                        BaseadminCrontab::Remove();
                        break;
                }
                break;
            case 'reset':
                switch ($inId) {
                    case 'Crontab':
                        BaseadminCrontab::Reset();
                        break;
                }
                break;
            case 'version':
                switch ($inId) {
                    case 'PHP':
                        $return .= PHP_VERSION;
                        break;
                    case 'Dokuwiki':
                        $return .= @substr(file_get_contents('VERSION'), 0, 10);
                        break;
                }
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', '', '', "Infra action unknown: $inAction $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function RowAr($inId): array {
        $strbtn = '';
        // if (strpos("PHP Dokuwiki", $inId) !== false) $strbtn .= AdminXhtml::ButtonGet("baseadmin\BaseadminInfra::Exec action=version id=$inId", '[Info]');
        $status = BaseadminCmd::ExecGet("baseadmin\BaseadminInfra::Exec action=status id=$inId");
        $strbtninfo = BaseadminXhtml::ButtonGet("baseadmin\BaseadminInfra::Exec action=info id=$inId", $status);
        if (strpos("Crontab", $inId) !== false) $strbtn .= BaseadminXhtml::ButtonGet("baseadmin\BaseadminInfra::Exec action=reset id=$inId", '[Reset]') . BaseadminXhtml::ButtonGet("baseadmin\BaseadminInfra::Exec action=remove id=$inId", '[Remove]');;
        $strlink = $inId == 'Dokuwiki' ? BaseadminXhtml::LinkGet('doku.php?do=admin') : '';
        return [$inId, BaseadminCmd::ExecGet("baseadmin\BaseadminInfra::Exec action=note id=$inId"), $strbtninfo, $strbtn, $strlink];
    }
    /* -------------------------------------------------------------------- */
}