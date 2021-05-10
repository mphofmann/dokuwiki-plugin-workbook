<?php
namespace workbook\wbinc\admin;
class AdminCmd {
    /* -------------------------------------------------------------------- */
    public static function SystemEcho($inCmd, $showCmd = true): void {
        if ($showCmd) echo "$inCmd\n";
        system($inCmd);
    }
    /* -------------------------------------------------------------------- */
    public static function SystemGet($inCmd, $showCmd = false): string {
        $return = '';
        if ($showCmd) $return .= "$inCmd\n";
        $return .= shell_exec($inCmd);
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ExecGet($inCmd, $inValue = '') {
        ob_start();
        echo \_Wb_::CmdExec($inCmd, $inValue);
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
}