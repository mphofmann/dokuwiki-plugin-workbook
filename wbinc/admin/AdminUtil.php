<?php
namespace workbook\wbinc\admin;
class AdminUtil {
    /* -------------------------------------------------------------------- */
    public static function IpPublicGet() {
        return file_get_contents("http://checkip.amazonaws.com");
       }
    /* -------------------------------------------------------------------- */
}