<?php
namespace workbook\wbinc\doku;
use function checkSecurityToken;
use function formSecurityToken;
class DokuXhtmlForm {
    /* -------------------------------------------------------------------- */
    public static function SecTokenCheck() {
        return checkSecurityToken();
    }
    /* -------------------------------------------------------------------- */
    public static function SecTokenEcho() {
        echo self::SecTokenGet();
    }
    /* -------------------------------------------------------------------- */
    public static function SecTokenGet() {
        return formSecurityToken(false);
    }
    /* -------------------------------------------------------------------- */
}