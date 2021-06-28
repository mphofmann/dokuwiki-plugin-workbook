<?php
namespace workbook\wbincdoku\doku;
class DokuGlobal {
    /* -------------------------------------------------------------------- */
    public static function ActGet(): string {
        global $ACT;
        $return = (is_array($ACT)) ? key($ACT) : $ACT;
        return (string)$return;
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