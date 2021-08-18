<?php
namespace workbook\wbinc\baseadmin;
class BaseadminConf {
    /* -------------------------------------------------------------------- */
    private static $__Ar = [];
    private static $__DefaultAr = ['plugin' => ['workbook' => ['connect_dist' => 'stable', 'connect_version' => 'stable']]];
    /* -------------------------------------------------------------------- */
    public static function Get($inVar1, $inVar2 = '', $inVar3 = ''): string {
        if (empty(self::$__Ar)) {
            $conf = [];
            $ar = [WB_DATACONF . 'local.php', WB_DATACONF . 'local.protected.php'];
            foreach ($ar as $file) {
                if (file_exists($file)) {
                    include($file); // TODO ?
                }
            }
            self::$__Ar = $conf;
        }
        if (empty($inVar2)) {
            @$return = self::$__Ar[$inVar1] ?? self::$__DefaultAr[$inVar1];
        } elseif (empty($inVar3)) {
            @$return = self::$__Ar[$inVar1][$inVar2] ?? self::$__DefaultAr[$inVar1][$inVar2];
        } else {
            @$return = self::$__Ar[$inVar1][$inVar2][$inVar3] ?? self::$__DefaultAr[$inVar1][$inVar2][$inVar3];
        }
        return $return ?? '';
    }
    /* -------------------------------------------------------------------- */
    public static function ConfDefaultUpdate(): bool {
        if (is_dir(WB_ROOT . 'workbook/module/workbookcore')) {
            $ar = [];
            $ar[] = '<?php';
            $ar[] = '// FULLY GENERATE PAGE';
            foreach (\_Wb_::WbconfAr('default') as $id => $val) {
                $ar[] = "\$conf['$id'] = '" . strtr($val, ["'" => "\\'"]) . "';";
            }
            if ( ! file_exists(WB_ROOT . 'lib/plugins/workbook/conf/default.php.orig')) copy(WB_ROOT . 'lib/plugins/workbook/conf/default.php', WB_ROOT . 'lib/plugins/workbook/conf/default.php.orig');
            file_put_contents(WB_ROOT . 'lib/plugins/workbook/conf/default.php', implode("\n", $ar) . "\n");
        }
        return true;
    }
    /* -------------------------------------------------------------------- */
}