<?php
namespace workbook\wbinc\doku;
use function getEntities;
class DokuConf {
    /* -------------------------------------------------------------------- */
    private static $__Consts = [ //  e.g.
        'DOKU_INC' => DOKU_INC, //   /home/sapp/data/home/workbook/
        'DOKU_URL' => DOKU_URL, //   http://domain.org/path/doku.php
        //
        'DOKU_BASE' => DOKU_BASE, // /path/ or http://domain.org/path/ (if cannonical is set)
        'DOKU_REL' => DOKU_REL, //   /path/
    ];
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
    public static function ConstGet($inConst) {
        return @self::$__Consts[$inConst];
    }
    /* -------------------------------------------------------------------- */
    public static function EntitiesAr() {
        return getEntities();
    }
    /* -------------------------------------------------------------------- */
    public static function VersionGet() {
        $return = @file_get_contents('VERSION');
        return substr($return, 0, 10);
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