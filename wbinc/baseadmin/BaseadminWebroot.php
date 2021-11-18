<?php
namespace workbook\wbinc\baseadmin;
class BaseadminWebroot {
    /* -------------------------------------------------------------------- */
    private static $__Ar = [ //
        // workbookcore
        'wb.php' => 'workbook/module/workbookcore/wbconf/', //
        'index.php' => 'workbook/module/workbookcore/wbconf/', //
        'dokuiframe.php' => 'workbook/module/workbookcore/wbconf/', //
        // workbookdokuwiki
        'bin/' => 'workbook/module/workbookdokuwiki/wbconf/', //
        'inc/' => 'workbook/module/workbookdokuwiki/wbconf/', //
        'lib/' => 'workbook/module/workbookdokuwiki/wbconf/', //
        'vendor/' => 'workbook/module/workbookdokuwiki/wbconf/', //
        'doku.php' => 'workbook/module/workbookdokuwiki/wbconf/', //
        'VERSION' => 'workbook/module/workbookdokuwiki/wbconf/', //
    ];
    /* -------------------------------------------------------------------- */
    public static function Exec($inAction, $inFilepath) {
        $return = '';
        switch ($inAction) {
            case 'link':
                if (isset(self::$__Ar[$inFilepath]) and file_exists(self::$__Ar[$inFilepath] . $inFilepath)) {
                    $filepath = trim($inFilepath, '/');
                    if ( ! file_exists("$inFilepath.orig") and (is_file($inFilepath) or is_dir($inFilepath))) rename($filepath, "$filepath.orig");
                    if (file_exists($inFilepath)) unlink($filepath);
                    symlink(self::$__Ar[$inFilepath] . $filepath, $filepath);
                } else {
                    BaseadminXhtmlMsg::Echo('Warning', '', '', "File '$inFilepath' not defined/missing.");
                }
                break;
            case 'unlink':
                $filepath = trim($inFilepath, '/');
                unlink($filepath);
                if (file_exists("$filepath.orig")) rename("$filepath.orig", $filepath);
                break;
            case 'status':
                $color = (file_exists($inFilepath)) ? 'green' : 'red';
                $return .= BaseadminXhtml::StatusGet($color);
                break;
            default:
                BaseadminXhtmlMsg::Echo('Warning', '', '', "Action unknown: $inAction $inFilepath");
                break;
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public static function RowAr($inId, $inNote, $inAttr = '') {
        $strstatus = ($inAttr == 'disabled') ? BaseadminXhtml::StatusGet('white') : BaseadminCmd::ExecGet("baseadmin\BaseadminWebroot::Exec action=status id=$inId");
        $attr = (file_exists(self::$__Ar[$inId])) ? $inAttr : 'disabled';
        $strexec = BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=link id=$inId", '[Link]', (is_link(trim($inId, '/')) or ($attr == 'disabled')) ? 'disabled' : '');
        $strexec .= BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=unlink id=$inId", '[Unlink]', (is_link(trim($inId, '/')) and ($attr != 'disabled')) ? '' : 'disabled');
        return [$inId, $inNote, $strstatus, $strexec, ''];
    }
    /* -------------------------------------------------------------------- */
}