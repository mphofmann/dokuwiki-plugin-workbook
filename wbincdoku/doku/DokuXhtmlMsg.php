<?php
namespace workbook\wbincdoku\doku;
use function html_msgarea;
use function msg;
class DokuXhtmlMsg {
    /* -------------------------------------------------------------------- */
    private static $__Types = ['Debug-Info' => 0, 'Debug-Notice' => 2, 'Info' => 0, 'Success' => 1, 'Notice' => 2, 'Warning' => -1, 'Error' => -1,];
    /* -------------------------------------------------------------------- */
    public static function EchoFalse($inType, $inMethod, $inPara, $inText = ''): bool {
        self::Echo($inType, $inMethod, $inPara, $inText);
        return false;
    }
    /* -------------------------------------------------------------------- */
    public static function Echo($inType, $inMethod, $inPara, $inText = ''): void {
        if (error_reporting() == 0) return;
        if (is_numeric($inType)) $inType = in_array($inType, self::$__Types);
        if (self::__FilterCheck($inType)) {
            echo self::Get($inType, $inMethod, $inPara, $inText);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Get($inType, $inMethod, $inPara, $inText): string {
        $return = (empty($inType)) ? '' : str_pad("[$inType] ", 10, ' ');
        $return .= (($pos = strrpos($inMethod, '\\')) === false) ? $inMethod : substr($inMethod, $pos + 1);
        $para = $inPara;
        if (is_array($para)) {
            ob_start();
            var_dump($para);
            $para = ob_get_clean();
        }
        $return .= (empty($para)) ? $para : "($para)";
        if ( ! empty($return)) $return .= ' ';
        $return .= $inText;
        $return .= "\n";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Add($inType, $inMethod, $inPara, $inText): bool {
        if ( ! self::__FilterCheck($inType)) return false;
        if (is_numeric($inType)) $inType = in_array($inType, self::$__Types);
        msg(self::Get('', $inMethod, $inPara, $inText), self::$__Types[$inType]);
        return true;
    }
    /* -------------------------------------------------------------------- */
    public static function AreaGet(): string {
        ob_start();
        html_msgarea();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    private static function __FilterCheck($inType): bool {
        if (substr($inType, 0, 5) != 'Debug') return true; // no Debug-* message
        if (DokuConf::ConfGet('allowdebug') != '1') return false; // Debug not enabled
        if (substr($inType, 0, strlen('Debug-Info')) == 'Debug-Info') return false; // skip Debug-Info messages ... too many
        return true;
    }
    /* -------------------------------------------------------------------- */
}