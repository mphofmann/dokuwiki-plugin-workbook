<?php
namespace workbook\wbinc\admin;
class AdminInfra {
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
    public static function RowAr($inId): array {
        $strbtn = '';
        // if (strpos("PHP Dokuwiki", $inId) !== false) $strbtn .= AdminXhtml::ButtonGet("admin\AdminInfra::Exec action=version id=$inId", '[Info]');
        $status = AdminCmd::ExecGet("admin\AdminInfra::Exec action=status id=$inId");
        $strbtninfo = AdminXhtml::ButtonGet("admin\AdminInfra::Exec action=info id=$inId", $status);
        if (strpos("Crontab", $inId) !== false) $strbtn .= AdminXhtml::ButtonGet("admin\AdminInfra::Exec action=reset id=$inId", '[Reset]') . AdminXhtml::ButtonGet("admin\AdminInfra::Exec action=remove id=$inId", '[Remove]');;
        $strlink = $inId == 'Dokuwiki' ? AdminXhtml::LinkGet('doku.php?do=admin') : '';
        return [$inId, AdminCmd::ExecGet("admin\AdminInfra::Exec action=note id=$inId"), $strbtninfo, $strbtn, $strlink];
    }
    /* -------------------------------------------------------------------- */
    public static function ConfLocalExec($inAction = 'status'): void {
        switch ($inAction) {
            case 'status':
                echo date('Y-m-d His', filemtime(WB_DATACONF . 'local.php'));
                break;
            case 'purge':
                AdminCache::ConfLocalTouch();
                AdminXhtmlMsg::Echo('Success', '', '', 'Cache purged.');
                break;
            case 'clear':
                AdminCache::ConfLocalTouch();
                AdminInode::Clear('data/cache/'); // TODO
                AdminXhtmlMsg::Echo('Success', '', '', 'All cache cleared.');
                break;
            default:
                AdminXhtmlMsg::EchoFalse('Notice', __METHOD__, $inAction, "Unknown action.");
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
                        $return .= AdminCrontab::StatusGet();
                        break;
                    default:
                        $ar = ['Infra' => 'infra-linux', 'PHP' => 'infra-php', 'PHP-Ioncube' => 'infra-ioncube', 'Webroot' => 'infra-webroot', 'Dokuwiki' => 'controller-doku',];
                        $color = \_Wb_::RunmodeCheck($ar[$inId]) ? 'green' : 'red';
                        $return .= AdminXhtml::StatusGet($color);
                        break;
                }
                break;
            case 'info':
                switch ($inId) {
                    case 'Infra':
                        // Infra
                        $return .= AdminExec::OutputHeadingGet('INFRA');
                        $return .= AdminExec::OutputLinesGet('UNAME', php_uname());
                        $return .= AdminExec::OutputLinesGet('ARCH', php_uname('m'));
                        $return .= AdminExec::OutputLinesGet('OS', php_uname('s')); // PHP_OS
                        $return .= AdminExec::OutputLinesGet('HOSTNAME', gethostname()); // php_uname('n')
                        $return .= AdminExec::OutputLinesGet('HOSTNAMECTL', shell_exec('hostnamectl'));
                        // Disk
                        $return .= AdminExec::OutputHeadingGet('DISK');
                        $return .= AdminExec::OutputLinesGet('FREE-SPACE', @round(disk_free_space('.') / 10 ** 9) . 'GB');
                        $return .= AdminExec::OutputLinesGet('LOCALE', setlocale(LC_ALL, 0));
                        // PHP
                        $return .= AdminExec::OutputHeadingGet('PHP');
                        $return .= AdminExec::OutputLinesGet('PHP-SAPI', php_sapi_name());
                        $return .= AdminExec::OutputLinesGet('PHP-VERSION', phpversion());
                        $return .= AdminExec::OutputLinesGet('PHP-EXTENSIONS', implode(', ', get_loaded_extensions()));
                        $return .= AdminExec::OutputLinesGet('PHP-TIMEZONE', date_default_timezone_get() . " (now: " . date('Y-m-d-His') . ")");
                        $return .= AdminExec::OutputLinesGet('PHP-CWD', getcwd());
                        // php.ini
                        $return .= AdminExec::OutputHeadingGet('PHP.INI');
                        $return .= AdminExec::OutputLinesGet('PHP.INI', php_ini_loaded_file());
                        $ar = ['memory_limit' => '', 'max_execution_time' => '', 'safe_mode' => '', 'ignore_user_abort' => '', 'disable_functions' => '', 'sendmail_path' => ''];
                        foreach ($ar as $id => $val) {
                            $return .= AdminExec::OutputLinesGet("PHP.INI: {$id}", ini_get($id));
                        }
                        // Access
                        $return .= AdminExec::OutputHeadingGet('ACCESS');
                        $return .= AdminExec::OutputLinesGet('USER', get_current_user());
                        $return .= AdminExec::OutputLinesGet('ID', shell_exec('id'));
                        $ar = [getcwd(), '/home', '/var/log/syslog', '/var/log/apache2', '/var/www'];
                        foreach ($ar as $val) {
                            $str = '';
                            if (@is_readable($val)) $str .= "readable ";
                            if (@is_writeable($val)) $str .= "writeable ";
                            if (@is_executable($val)) $str .= "executable ";
                            $return .= AdminExec::OutputLinesGet($val, $str);
                        }
                        // Cron
                        $return .= AdminExec::OutputHeadingGet('CRON');
                        $return .= AdminExec::OutputLinesGet('CRONTAB', shell_exec('crontab -l 2>&1'));
                        // Commands
                        $return .= AdminExec::OutputHeadingGet('COMMANDS');
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
                            $return .= AdminExec::OutputLinesGet($id, $str);
                        }
                        // Workbooks
                        $return .= AdminExec::OutputHeadingGet('WORKBOOK');
                        $return .= AdminExec::OutputLinesGet('WB_RUNARCHLIST', WB_RUNARCHLIST);
                        $return .= AdminExec::OutputLinesGet('WB_RUNMODELIST', WB_RUNMODELIST);
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
                        $add = AdminCrontab::InfoGet();
                        $return .= "<pre>$add</pre>";
                        break;
                }
                break;
            case 'remove':
                switch ($inId) {
                    case 'Crontab':
                        AdminCrontab::Remove();
                        break;
                }
                break;
            case 'reset':
                switch ($inId) {
                    case 'Crontab':
                        AdminCrontab::Reset();
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
                AdminXhtmlMsg::Echo('Warning', '', '', "Infra action unknown: $inAction $inId");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}