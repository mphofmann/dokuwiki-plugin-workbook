<?php
namespace workbook\wbinc\doku;
class DokuGlobal {
    /* -------------------------------------------------------------------- */
    public static function ActGet() {
        global $ACT;
        return $ACT;
    }
    /* -------------------------------------------------------------------- */
    public static function BrowserIdGet() {
        return auth_browseruid();
    }
    /* -------------------------------------------------------------------- */
    public static function NsidGet() {
        global $INFO, $ID;
        if (!empty($_REQUEST['id'])) return $_REQUEST['id'];
        if (!empty($INFO['id'])) return $INFO['id'];
        return $ID;
    }
    /* -------------------------------------------------------------------- */
}