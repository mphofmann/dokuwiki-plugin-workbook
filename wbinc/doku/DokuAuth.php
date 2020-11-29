<?php
namespace workbook\wbinc\doku;
use function auth_browseruid;
use function auth_isadmin;
use function auth_ismanager;
use function auth_quickaclcheck;
class DokuAuth {
    /* -------------------------------------------------------------------- */
    public static function AdminIs() {
        return auth_isadmin();
    }
    /* -------------------------------------------------------------------- */
    public static function ManagerIs() {
        return auth_ismanager();
    }
    /* -------------------------------------------------------------------- */
    public static function NsAclGet($inNs) {
        $ns = substr($inNs, 0, 1) == ':' ? substr($inNs, 1) : $inNs;
        $ns = substr($ns, -1) == ':' ? substr($ns, 0, -1) : $ns;
        $return = self::IdAclGet("$ns:*");
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function IdAclGet($inId) {
        return auth_quickaclcheck($inId);
    }
    /* -------------------------------------------------------------------- */
}