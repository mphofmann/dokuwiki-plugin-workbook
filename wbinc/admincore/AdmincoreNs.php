<?php
namespace workbook\wbinc\admincore;
use workbook\wbinc\admin;
use workbookcore\wbinc\util;
use workbookcore\wbinc\xhtml;
class AdmincoreNs {
    /* -------------------------------------------------------------------- */
    public static function ZArchive($inNs) {
        return self::__Move('zarchive', $inNs);
    }
    /* -------------------------------------------------------------------- */
    public static function ZTrash($inNs) {
        return self::__Move('ztrash', $inNs);
    }
    /* -------------------------------------------------------------------- */
    private static function __Move($inType = 'zarchive', $inNs = '') {
        if (empty($inNs)) return xhtml\XhtmlMsg::Echo('Warning', __METHOD__, "$inNs", "Missing inputs.");
        xhtml\XhtmlMsg::Echo('Info', __METHOD__, "$inNs", "Moving to $inType.");
        $pathoutyear = "data/pages/zworkbook/$inType/" . date('Y') . "/";
        util\UtilPath::MkdirCheck($pathoutyear);
        $pathin = "data/pages/" . strtr($inNs, [':' => '/']) . "/";
        $pathout = $pathoutyear . date('Y-m-d-His') . "-" . strtr($inNs, [':' => '-']);
        echo admin\AdminCmd::SystemGet("mv {$pathin} {$pathout}");
        AdmincoreOperating::WbSyncExec('clear', $inNs);
    }
    /* -------------------------------------------------------------------- */
}