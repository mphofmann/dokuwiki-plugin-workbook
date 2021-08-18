<?php
namespace workbook\wbinc\baseadmin;
class BaseadminXhtmlMsg {
    /* -------------------------------------------------------------------- */
    public static function EchoFalse($inType, $inMethod, $inPara, $inText = ''): bool {
        self::Echo($inType, $inMethod, $inPara, $inText);
        return false;
    }
    /* -------------------------------------------------------------------- */
    public static function Echo($inType, $inMethod, $inPara, $inText = ''): void {
        if (error_reporting() == 0) return;
        echo self::Get($inType, $inMethod, $inPara, $inText) . "\n";
    }
    /* -------------------------------------------------------------------- */
    public static function Get($inType, $inMethod, $inPara, $inText = ''): string {
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
        return $return;
    }
    /* -------------------------------------------------------------------- */
}