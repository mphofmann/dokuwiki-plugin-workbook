<?php
namespace workbook\wbinc\admin;
class AdminXhtmlMsg {
    /* -------------------------------------------------------------------- */
    public static function Echo($inType, $inMethod, $inPara, $inText = '') {
        echo self::Get($inType, $inMethod, $inPara, $inText);
    }
    /* -------------------------------------------------------------------- */
    public static function Get($inType, $inMethod, $inPara, $inText = '') {
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
}