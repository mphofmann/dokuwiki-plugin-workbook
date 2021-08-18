<?php
namespace workbook\wbinc\baseadmin;
class BaseadminExec {
    /* -------------------------------------------------------------------- */
    private static $__Style = 'overflow:visible;max-height:inherit;background-image:none;display:block;margin:0;border:0;border-radius:0;padding:3px;font-family:monospace;font-size:100%;white-space:pre;';
    /* -------------------------------------------------------------------- */
    public static function HandleGet($inCmds = []): string {
        $return = '';
        $rc = false;
        foreach ($inCmds as $id => $val) {
            if (isset($_REQUEST[$id])) $rc = true;
        }
        if ($rc === false) return false; // Dokuwiki: first call nothings is set
        foreach ($inCmds as $id => $val) {
            if (!is_array(@$_REQUEST[$id])) continue;
            $cmd = key($_REQUEST[$id]);
            $value = $_REQUEST[$id][$cmd];
            if ($val == 'notempty' and empty($_REQUEST[$id][$cmd])) continue;
            $strval = (empty($value)) ? '' : "[$value]";
            $return .= "<div style='" . self::$__Style . "padding:5px;font-size:larger;font-weight:bold;'>Command: \"{$cmd}{$strval}\"</div>\n";
            $return .= BaseadminCmd::ExecGet($cmd, $value);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function OutputEcho($inOutput = '', bool $doFormat = true): void {
        echo self::OutputGet($inOutput, $doFormat);
    }
    /* -------------------------------------------------------------------- */
    public static function OutputGet($inOutput = '', bool $doFormat = true): string {
        $return = '';
        $inOutput = trim($inOutput);
        if (!empty($inOutput)) {
            $return .= "<div style='overflow-x:auto'>";
            if ($doFormat) {
                $i = 0;
                foreach (explode("\n", $inOutput) as $line) {
                    if (substr($line, -3) == 'End') $i--;
                    $px = 10 * $i;
                    $style = "margin-left:{$px}px;";
                    $return .= self::OutputLineGet($line, $style);
                    if (substr($line, -5) == 'Start') $i++;
                }
            } else {
                $return .= self::OutputLineGet($inOutput);
            }
            $return .= "</div>";
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function OutputLineGet($inOutput, $inStyle = ''): string {
        $class = 'info';
        $class = (stripos($inOutput, 'success')) ? 'success' : $class;
        $class = (stripos($inOutput, 'notice')) ? 'notice' : $class;
        $class = (stripos($inOutput, 'warning')) ? 'warning' : $class;
        $class = (stripos($inOutput, 'error')) ? 'error' : $class;
        return "<div class='$class' style='" . self::$__Style . " $inStyle'>$inOutput</div>";
    }
    /* -------------------------------------------------------------------- */
    public static function OutputLinesGet($inId, $inValues, $inPad=30): string {
        $return = '';
        $ar = explode("\n", $inValues);
        $return .= self::OutputLineGet('- ' . str_pad($inId, $inPad, ' ') . ": " . array_shift($ar));
        foreach ($ar as $val) {
            if (empty($val)) continue;
            $return .= self::OutputLineGet('- ' . str_pad('', $inPad, ' ') . ": $val");
        }
        return $return;
    }

    /* -------------------------------------------------------------------- */
    public static function OutputHeadingGet($inString): string {
        $str = str_pad($inString, 180, '-');
        return "<div class='info' style='" . self::$__Style . "'>$str</div>";
    }
    /* -------------------------------------------------------------------- */
}