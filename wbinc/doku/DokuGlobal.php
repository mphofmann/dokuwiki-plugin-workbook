<?php
namespace workbook\wbinc\doku;
class DokuGlobal {
    /* -------------------------------------------------------------------- */
    private static $__Consts = [ //  e.g.
        'DOKU_INC' => DOKU_INC, //   /home/sapp/data/home/workbook/
        'DOKU_URL' => DOKU_URL, //   http://domain.org/path/doku.php
        //
        'DOKU_BASE' => DOKU_BASE, // /path/ or http://domain.org/path/ (if cannonical is set)
        'DOKU_REL' => DOKU_REL, //   /path/
    ];
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