<?php
namespace workbook\wbinc\dokuadmin;
use workbook\wbinc\admin;
class DokuadminWebroot {
    /* -------------------------------------------------------------------- */
    public static $Webroots = [ //
        'index.php' => 'Controller switcher', //
        // 'doku.php' => 'Dokuwiki controller', //
        'wb.php' => 'Workbook controller', //
    ];
    /* -------------------------------------------------------------------- */
    public static function Action($inAction, $inFilepath) {
        $return = '';
        switch ($inAction) {
            case 'install':
                $filepath = "lib/plugins/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (file_exists($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    copy($filepath, $inFilepath);
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'link':
                $filepath = "lib/plugins/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (is_file($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    if (file_exists($inFilepath)) unlink($inFilepath);
                    symlink($filepath, $inFilepath);
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'remove':
                $return .= unlink($inFilepath);
                break;
            case 'restore':
                if (file_exists("$inFilepath.orig")) {
                    unlink($inFilepath);
                    copy("$inFilepath.orig", $inFilepath);
                    unlink("$inFilepath.orig");
                } else {
                    admin\AdminXhtmlMsg::Echo('Warning', '', '', "File '$inFilepath.orig' is missing.");
                }
                break;
            case 'status':
                $rc = (strpos("index.php wb.php", $inFilepath) === false) ? file_exists($inFilepath) : is_link($inFilepath);
                $color = $rc ? 'green' : 'red';
                $return .= admin\AdminXhtml::StatusGet($color);
                break;
            default:
                admin\AdminXhtmlMsg::Echo('Warning', '', '', "Action unknown: $inAction $inFilepath");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}