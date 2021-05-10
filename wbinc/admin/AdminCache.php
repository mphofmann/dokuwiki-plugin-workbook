<?php
namespace workbook\wbinc\admin;
class AdminCache {
    /* -------------------------------------------------------------------- */
    private static $__Path = WB_PATHCACHE;
    /* -------------------------------------------------------------------- */
    public static function Exists($inNamespace, $inType, $inBasename): bool {
        $return = false;
        if (file_exists(self::__PathCheckGet($inNamespace) . "$inType/$inBasename")) {
            $return = filemtime(self::__PathCheckGet($inNamespace) . "$inType/$inBasename") > self::__ConfLocalMtimeInt();
            if ($return == false) {
                self::Unlink($inNamespace, $inType, $inBasename);
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function FilepathGet($inNamespace, $inType, $inBasename): string {
        return self::__PathCheckGet($inNamespace) . "$inType/$inBasename";
    }
    /* -------------------------------------------------------------------- */
    public static function Get($inNamespace, $inType, $inBasename): string {
        $return = '';
        if (self::Exists($inNamespace, $inType, $inBasename)) {
            $return = file_get_contents(self::__PathCheckGet($inNamespace) . "$inType/$inBasename");
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function Put($inNamespace, $inType, $inBasename, $inString): bool {
        AdminInode::MkdirCheck(self::__PathCheckGet($inNamespace) . $inType);
        return file_put_contents(self::__PathCheckGet($inNamespace) . "$inType/$inBasename", $inString);
    }
    /* -------------------------------------------------------------------- */
    public static function Unlink($inNamespace, $inType, $inBasename): bool {
        return unlink(self::__PathCheckGet($inNamespace) . "$inType/$inBasename");
    }
    /* -------------------------------------------------------------------- */
    private static function __PathCheckGet($inNamespace): string {
        $return = self::$__Path . strtr($inNamespace, ['\\' => '-']) . '/';
        if ( ! is_dir($return)) AdminInode::MkdirCheck($return);
        if (self::__ConfLocalMtimeInt() > filemtime($return)) {
            AdminInode::RmR($return);
            AdminInode::MkdirCheck($return);
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __ConfLocalMtimeInt(): int {
        return filemtime(WB_DATACONF . 'local.php');
    }
    /* -------------------------------------------------------------------- */
}