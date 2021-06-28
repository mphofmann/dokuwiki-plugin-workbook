<?php
namespace workbook\wbinc\admin;
class AdminCrontab {
    /* -------------------------------------------------------------------- */
    private static $__CronPhp = 'workbook/module/workbookcore/bin/wbjobclicron.php';
    //                               m   h d m w user   command
    private static $__CrontabLine = '*/5 * * * *        cd @GETCWD@; php -f @CRONPHP@';
    private static $__Cron_D_Line = '*/5 * * * * @USER@ cd @GETCWD@; php -f @CRONPHP@';
    private static $__CronCmd = 'cd @GETCWD@; php -f @CRONPHP@';
    /* -------------------------------------------------------------------- */
    public static function A_Construct(): bool {
        $strtr = ['@GETCWD@' => getcwd(), '@CRONPHP@' => self::$__CronPhp, '@USER@' => get_current_user(),];
        self::$__CrontabLine = strtr(self::$__CrontabLine, $strtr);
        self::$__Cron_D_Line = strtr(self::$__Cron_D_Line, $strtr);
        self::$__CronCmd = strtr(self::$__CronCmd, $strtr);
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function Exec(): void {
        system(self::$__CronCmd);
    }
    /* -------------------------------------------------------------------- */
    public static function StatusGet(): string {
        $status = 'green';
        if (!file_exists(self::$__CronPhp)) $status = 'red';
        if (!self::Check()) $status = 'yellow';
        return AdminXhtml::StatusGet($status, 'Crontab');
    }
    /* -------------------------------------------------------------------- */
    public static function InfoGet(): string {
        $return = '';
        $strmodule = file_exists(self::$__CronPhp) ? 'installed' : 'missing';
        $strcrontab = trim(self::Get(true));
        $return .= "<pre>MODULE workbookcron: $strmodule</pre>";
        $return .= "<pre>CRONTAB ".get_current_user().": $strcrontab</pre>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Get($showErrors = false): string {
        $str = $showErrors ? "2>&1" : '';
        return (string)shell_exec("crontab -l $str");
    }
    /* -------------------------------------------------------------------- */
    public static function Check(): string {
        return (string)shell_exec('crontab -l');
    }
    /* -------------------------------------------------------------------- */
    public static function Reset(): bool {
        $type = file_exists(self::$__CronPhp) ? 'add' : 'remove';
        return self::__CrontabUpdate($type);
    }
    /* -------------------------------------------------------------------- */
    public static function Remove(): bool {
        return self::__CrontabUpdate('remove');
    }
    /* -------------------------------------------------------------------- */
    private static function __CrontabUpdate($inAction): bool { // add or remove
        $str = self::Get();
        $ar = [];
        foreach (explode("\n", $str) as $line) {
            if (!empty($line) and strpos($line, getcwd()) === false) {
                $ar[] = $line;
            }
        }
        if ($inAction == 'add') {
            $ar[] = strtr(self::$__CrontabLine, ['$GETCWD$' => getcwd()]);
        }
        if (empty($ar)) {
            system("crontab -r 2>&1");
        } else {
            $tmpfile = tempnam(sys_get_temp_dir(), 'wb');
            file_put_contents($tmpfile, implode("\n", $ar) . "\n");
            system("crontab $tmpfile 2>&1");
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
}