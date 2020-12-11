<?php
namespace workbook\wbinc\admin;
class AdminUtil {
    /* -------------------------------------------------------------------- */
    private static $__UrlMtimeAr = [];
    /* -------------------------------------------------------------------- */
    public static function IpPublicGet(): string {
        return file_get_contents("http://checkip.amazonaws.com");
    }
    /* -------------------------------------------------------------------- */
    public static function UrlMtime($inUrl): ?int {
        if (!isset(self::$__UrlMtimeAr[$inUrl])) {
            $ar = get_headers($inUrl);
            if (is_array($ar)) {
                foreach ($ar as $val) {
                    if (stripos($val, "Last-Modified:") !== false) {
                        list($str, $date) = explode(':', $val, 2);
                        self::$__UrlMtimeAr[$inUrl] = strtotime($date);
                        break;
                    }
                }
            }
        }
        return self::$__UrlMtimeAr[$inUrl];
    }
    /* -------------------------------------------------------------------- */
}