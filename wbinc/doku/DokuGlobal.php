<?php
namespace workbook\wbinc\doku;
use function getEntities;
class DokuGlobal {
    /* -------------------------------------------------------------------- */
    private static $__Consts = [ //
        'DOKU_INC' => DOKU_INC, //
        'DOKU_BASE' => DOKU_BASE, //
        'DOKU_REL' => DOKU_REL, //
        'DOKU_URL' => DOKU_URL, //
    ];
    /* -------------------------------------------------------------------- */
    public static function BrowserIdGet() {
        return auth_browseruid();
    }
    /* -------------------------------------------------------------------- */
    public static function ConstGet($inConst) {
        return @self::$__Consts[$inConst];
    }
    /* -------------------------------------------------------------------- */
    public static function ConfGet($inVar1, $inVar2 = '', $inVar3 = '') {
        global $conf;
        if (empty($inVar2)) {
            $found = isset($conf[$inVar1]);
            $return = @$conf[$inVar1];
        } elseif (empty($inVar3)) {
            $found = isset($conf[$inVar1][$inVar2]);
            $return = @$conf[$inVar1][$inVar2];
        } else {
            $found = isset($conf[$inVar1][$inVar2][$inVar3]);
            $return = @$conf[$inVar1][$inVar2][$inVar3];
        }
        if (!$found and $inVar1 == 'plugin' and !empty($inVar2)) {
            $return = self::__PluginConfDefaultGet($inVar2, $inVar3);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function EntitiesAr() {
        return getEntities();
    }    /* -------------------------------------------------------------------- */
    public static function NsidGet() {
        global $INFO, $ID;
        if (!empty($_REQUEST['id'])) return $_REQUEST['id'];
        if (!empty($INFO['id'])) return $INFO['id'];
        return $ID;
    }
    /* -------------------------------------------------------------------- */
    public static function NsGet() {
        $nsid = self::NsidGet();
        $pos = strrpos((string)$nsid, ':');
        return ($pos === false) ? '' : substr((string)$nsid, 0, $pos);
    }
    /* -------------------------------------------------------------------- */
    public static function VersionGet() {
        $return = @file_get_contents('VERSION');
        return substr($return, 0, 10);
    }
    /* -------------------------------------------------------------------- */
    public static function InfoGet($inVar1, $inVar2 = '', $inVar3 = '') {
        global $INFO;
        if (empty($inVar2)) {
            $return = @$INFO[$inVar1];
        } elseif (empty($inVar3)) {
            $return = @$INFO[$inVar1][$inVar2];
        } else {
            $return = @$INFO[$inVar1][$inVar2][$inVar3];
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __PluginConfDefaultGet($inPlugin, $inVar) {
        $return = '';
        $filepath = WB_INC . "lib/plugins/$inPlugin/conf/default.php";
        if (file_exists($filepath)) {
            global $conf;
            $confback = $conf;
            $conf = [];
            include($filepath);
            $return = @$conf[$inVar];
            $conf = $confback;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}