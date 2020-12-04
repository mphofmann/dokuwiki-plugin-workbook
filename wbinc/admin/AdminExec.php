<?php
namespace workbook\wbinc\admin;
class AdminExec {
    /* -------------------------------------------------------------------- */
    public static function HandleExec($inCmds = []) {
        $return = '';
        $rc = false;
        foreach ($inCmds as $id => $val) {
            if (isset($_REQUEST[$id])) $rc = true;
        }
        if ($rc === false) return false; // first time - nothing to do
        // if (!dokucore\DokucoreXhtmlForm::SecTokenCheck()) return false; // TODO GPL
        foreach ($inCmds as $id => $val) {
            if (!is_array($_REQUEST[$id])) continue;
            $cmd = key($_REQUEST[$id]);
            $value = $_REQUEST[$id][$cmd];
            if ($val == 'notempty' and empty($_REQUEST[$id][$cmd])) continue;
            $strval = (empty($value)) ? '' : "[$value]";
            $return .= "<div style='font-weight:bold'>Command \"{$cmd}{$strval}\"</div>\n";
            $return .= AdminCmd::ExecGet($cmd, $value);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function OutputEcho($inOutput = '', $doFormat = true) {
        $inOutput = trim($inOutput);
        if (!empty($inOutput)) {
            echo "<div style='overflow: auto; font-family:monospace;'>";
            if ($doFormat) {
                $i = 0;
                foreach (explode("\n", $inOutput) as $line) {
                    if (substr($line, -3) == 'End') $i--;
                    $px = 10 * $i;
                    $style = "margin-left:{$px}px;";
                    self::OutputLineEcho($line, $style);
                    if (substr($line, -5) == 'Start') $i++;
                }
            } else {
                self::OutputLineEcho($inOutput);
            }
            echo "</div>";
        }
    }
    /* -------------------------------------------------------------------- */
    public static function OutputLineEcho($inOutput, $inStyle = '') {
        $class = 'info';
        $class = (stripos($inOutput, 'success')) ? 'success' : $class;
        $class = (stripos($inOutput, 'notice')) ? 'notice' : $class;
        $class = (stripos($inOutput, 'warning')) ? 'warning' : $class;
        $class = (stripos($inOutput, 'error')) ? 'error' : $class;
        echo("<div class='$class' style='display:block; overflow:visible; white-space:nowrap; $inStyle'>$inOutput</div>");
    }
    /* -------------------------------------------------------------------- */
}