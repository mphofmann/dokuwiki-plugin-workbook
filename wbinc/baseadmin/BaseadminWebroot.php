<?php
namespace workbook\wbinc\baseadmin;
class BaseadminWebroot {
    /* -------------------------------------------------------------------- */
    public static $Webroots = [ //
        'wb.php' => 'Workbook controller', //
        'dokumodal.php' => 'Doku-Modal controller', //
        'index.php' => 'Controller switcher', //
        'doku.php' => 'Dokuwiki controller', //
    ];
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction, $inFilepath) {
        $return = '';
        switch ($inAction) {
            case 'install':
                $filepath = "workbook/module/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (file_exists($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    copy($filepath, $inFilepath);
                } else {
                    BaseadminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'link':
                $filepath = "workbook/module/workbookcore/wbconf/$inFilepath";
                if (file_exists($filepath)) {
                    if (is_file($inFilepath) and !file_exists("$inFilepath.orig")) {
                        copy($inFilepath, "$inFilepath.orig");
                    }
                    if (file_exists($inFilepath)) unlink($inFilepath);
                    symlink($filepath, $inFilepath);
                } else {
                    BaseadminXhtmlMsg::Echo('Warning', '', '', "File '$filepath' is missing.");
                }
                break;
            case 'unlink':
                unlink($inFilepath);
                if (file_exists("$inFilepath.orig")) {
                    copy("$inFilepath.orig", $inFilepath);
                    unlink("$inFilepath.orig");
                }
                break;
            case 'status':
                $rc = (strpos("index.php wb.php", $inFilepath) === false) ? file_exists($inFilepath) : is_link($inFilepath);
                $color = $rc ? 'green' : 'red';
                $return .= BaseadminXhtml::StatusGet($color);
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', '', '', "Action unknown: $inAction $inFilepath");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
}