<?php
namespace workbook\wbinc\doku;
use function html_msgarea;
use function msg;
class DokuXhtmlMsg {
    /* -------------------------------------------------------------------- */
    private static $__Types = ['Debug-Info' => 0, 'Debug-Notice' => 2, 'Info' => 0, 'Success' => 1, 'Notice' => 2, 'Warning' => -1, 'Error' => -1,];
    /* -------------------------------------------------------------------- */
    public static function Echo($inType, $inMethod, $inPara, $inText = '') {
        if (is_numeric($inType)) $inType = in_array($inType, self::$__Types);
        echo self::Get($inType, $inMethod, $inPara, $inText);
        return $inType == 'Success' ? true : false;
    }
    /* -------------------------------------------------------------------- */
    public static function Get($inType, $inMethod, $inPara, $inText) {
        $return = (empty($inType)) ? '' : str_pad("[$inType] ", 10, ' ');
        $return .= (($pos = strrpos($inMethod, '\\')) === false) ? $inMethod : substr($inMethod, $pos + 1);
        $para = $inPara;
        if (is_array($para)) {
            ob_start();
            var_dump($para);
            $para = ob_get_clean();
        }
        $return .= (empty($para)) ? $para : "($para)";
        if (!empty($return)) $return .= ' ';
        $return .= $inText;
        $return .= "\n";
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Add($inType, $inMethod, $inPara, $inText) {
        if (self::__DebugFilterCheck($inType) === false) return false;
        if (is_numeric($inType)) $inType = in_array($inType, self::$__Types);
        msg(self::Get($inType, $inMethod, $inPara, $inText), self::$__Types[$inType]);
        return $inType == 1 ? true : false;
    }
    /* -------------------------------------------------------------------- */
    public static function AreaGet() {
        ob_start();
        html_msgarea();
        return ob_get_clean();
    }
    /* -------------------------------------------------------------------- */
    private static function __DebugFilterCheck($inType) {
        // Filter debug messages (false if to be omitted, true is ok)
        if (substr($inType, 0, 5) != 'Debug') return true; // no Debug-* message
        if (substr($inType, 0, strlen('Debug-Info')) == 'Debug-Info') return false; // skip Debug-Info messages ... too many
        if (!defined('DOKU_INC')) return true; // Not Dokuwiki
        if (DokuSysGlobal::ConfGet('allowdebug') == '1') return true; // Debug enabled
        return false;
    }
    /* -------------------------------------------------------------------- */
}