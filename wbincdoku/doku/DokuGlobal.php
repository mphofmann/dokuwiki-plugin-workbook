<?php
namespace workbook\wbincdoku\doku;
class DokuGlobal {
    /* -------------------------------------------------------------------- */
    public static function ActionGet(): string {
        global $ACT;
        return (string)$ACT; // is sometimes a string
    }
    /* -------------------------------------------------------------------- */
    public static function BrowserIdGet(): string {
        return auth_browseruid();
    }
    /* -------------------------------------------------------------------- */
    public static function NsidGet(): string {
        global $INFO, $ID;
        $return = $_REQUEST['id'] ?? $INFO['id'] ?? $ID;
        if (empty($return)) $return = 'start';
        return $return;
    }
    /* -------------------------------------------------------------------- */
}