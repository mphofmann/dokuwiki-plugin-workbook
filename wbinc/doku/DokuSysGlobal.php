<?php
namespace workbook\wbinc\doku;
class DokuSysGlobal {
    /* -------------------------------------------------------------------- */
    public static function ConstGet($inConst) {
        $return = '';
        switch ($inConst) {
            case 'DOKU_BASE':
                $return = DOKU_BASE;
                break;
            case 'DOKU_INC':
                $return = DOKU_INC;
                break;
            case 'DOKU_REL':
                $return = DOKU_REL;
                break;
            case 'DOKU_URL':
                $return = DOKU_URL;
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function ActGet() {
        global $ACT;
        return @$ACT;
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