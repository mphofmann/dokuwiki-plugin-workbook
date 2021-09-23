<?php
namespace workbook\wbdef\mainadmin;
use workbook\wbdef\mainadmin;
use workbook\wbinc\baseadmin;
use workbook\wbinc\baseutil;
use workbookadmin\wbinc\admin;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
use workbook\wbinc\basexhtml;
class MainadminWorkbook extends mainadmin\a_Mainadmin { // TODO delete
    /* -------------------------------------------------------------------- */
    protected static $_Item = 'workbook';
    protected static $_Icon = 'fas fa-cube';
    protected static $_Note = 'Configures a workbook in detail.';
    /* -------------------------------------------------------------------- */
    protected static function _Array1Get(): array {
        $returns = [];
        $returns[] = ['TH:WORKBOOK', 'TH:Action', 'TH:Tools', 'TH:Database & Datapages'];
        foreach (sys\SysNs::ScandirAr(':', 'local', 'pages', 'dirs hidepages') as $wb) {
            $wb = (substr($wb, -1) == ':') ? substr($wb, 0, -1) : $wb;
            // id
            $strid = "<a href='?id=$wb:start'><b><i class='fa " . mod\ModWb::FieldGet($wb, 'icon') . "' ></i> " . strtoupper($wb) . "</b></a>";
            // workbook
            $cmd = 'reset';
            $attr = mod\ModWb::CommandEnabledCheck($wb, $cmd) ? '' : 'disabled';
            $strwb = baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWb::{$cmd} wb={$wb}", "[".ucfirst($cmd)."]", $attr);
            ob_start();
            admin\AdminOperating::WbSyncExec('size', $wb);
            $strsize = ob_get_clean();
            if (!empty($strsize)) $strwb .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWb::Syncclear wb={$wb}", basexhtml\BasexhtmlIcon::Get('fa-undo-alt'), " title='Clear sync ($strsize)'", 'xsmall', 'Clear sync?');
            $strwb .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWb::Zarchive wb={$wb}", basexhtml\BasexhtmlIcon::Get('fa-archive'), ' title="Archive"', 'xsmall', 'Move to zarchive?');
            // tools
            $adds = ['1-mandatory' => '', '1-optional' => '', '1-additional' => '', '0-mandatory' => '', '0-optional' => ''];
            $dirs = sys\SysNs::ScandirAr($wb, 'local', 'pages', 'dirs');
            $ar = admin\AdminWb::ToolAr($wb);
            $ar = baseutil\BaseutilSort::KsortAr($ar);
            foreach ($ar as $tool => $modes) {
                unset($dirs[array_search("$tool:", $dirs)]);
                $add = "<a style='font-weight:bold;' href='?id={$wb}:{$tool}:start'>" . ucfirst($tool) . "</a> ";
                $btn = self::__ButtonsGet('tool', $wb, $tool);
                $type = (strpos($modes, 'reset') === false) ? 'optional' : 'mandatory';
                if (is_dir(WB_DATAPAGE . "$wb/$tool")) {
                    $adds["1-$type"] .= self::__InlineBlockGet($add, $btn, "1-$type");
                } else {
                    $adds["0-$type"] .= self::__InlineBlockGet($add, $btn, "0-$type");
                }
            }
            if (!empty($dirs)) {
                foreach ($dirs as $tool) {
                    $tool = substr($tool, 0, -1);
                    $add = "<a style='font-weight:bold;' href='?id={$wb}:{$tool}:start'>" . ucfirst($tool) . "</a> ";
                    $btn = self::__ButtonsGet('tool', $wb, $tool);
                    $adds['1-additional'] .= self::__InlineBlockGet($add, $btn, '1-additional');
                }
            }
            $strtools = $adds['1-mandatory'] . $adds['1-optional'] . $adds['1-additional'] . $adds['0-mandatory'] . $adds['0-optional'];
            // database
            $strdb = '';
            foreach (admin\AdminWb::TableAr($wb, 'database') as $table => $fields) {
                $add = "<a style='font-weight:bold;' href='?do=admin&page=struct_schemas&table={$wb}_{$table}'>" . ucfirst($table) . "</a> ";
                $btn = self::__ButtonsGet('table', $wb, $table);
                $type = (sys\SysTable::Exists($wb, $table)) ? '1-mandatory' : '0-mandatory';
                $strdb .= self::__InlineBlockGet($add, $btn, $type);
            }
            // datapages
            $strdp = '';
            foreach (admin\AdminWb::TableAr($wb, 'datapage') as $table => $fields) {
                $add = "<a style='font-weight:bold;' href='?do=admin&page=struct_schemas&table={$wb}_{$table}'>" . ucfirst($table) . "</a> ";
                $btn = self::__ButtonsGet('table', $wb, $table);
                $type = (sys\SysTable::Exists($wb, $table)) ? '1-mandatory' : '0-mandatory';
                $strdp .= self::__InlineBlockGet($add, $btn, $type);
            }
            // add
            $returns[] = [$strid, $strwb, $strtools, $strdb . $strdp];
        }
        if (count($returns) > 1) {
            $returns[] = ['ALL', baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWb::Resets", '[Reset]'), '', ''];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected static function _StylesAr(): array {
        return ['width:130px; white-space:nowrap;', 'width:60px', 'min-width:425px', 'min-width:290px'];
    }
    /* -------------------------------------------------------------------- */
    private static function __ButtonsGet($inType, $inWb, $inToolTable): string {
        $return = '';
        $tooltable = mod\ModTool::TypeGet($inToolTable);
        $cmds = '';
        switch ($inType) {
            case 'tool';
                $cmds = mod\ModWbTool::CommandsGet($inWb, $tooltable);
                break;
            case 'table';
                $cmds = mod\ModWbSheet::CommandsGet($inWb, $tooltable);
                break;
        }
        foreach (explode(',', $cmds) as $cmd) {
            if (empty($cmd)) continue;
            if ($cmd == 'install' or $cmd == 'reset') continue;
            switch ($inType) {
                case 'tool':
                    $attr = mod\ModWbTool::CommandEnabledCheck($inWb, $tooltable, $cmd) ? '' : 'disabled';
                    $return .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWbTool::{$cmd} wb={$inWb} tool={$inToolTable}", "[".ucfirst($cmd)."]", $attr, 'small');
                    break;
                case 'table':
                    $attr = mod\ModWbSheet::CommandEnabledCheck($inWb, $tooltable, $cmd) ? '' : 'disabled';
                    $return .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWbSheet::{$cmd} wb={$inWb} table={$inToolTable}", "[".ucfirst($cmd)."]", $attr, 'small');
                    break;
            }
        }
        if (is_dir(WB_DATAPAGE . "$inWb/$inToolTable")) {
            switch ($inType) {
                case 'tool';
                    ob_start();
                    admin\AdminOperating::WbSyncExec('size', "$inWb:$inToolTable");
                    $strsize = ob_get_clean();
                    if (!empty($strsize)) $return .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWbTool::Syncclear wb={$inWb} tool={$inToolTable}", basexhtml\BasexhtmlIcon::Get('fa-undo-alt'), " title='Clear sync ($strsize)'", 'xsmall', 'Clear sync?');
                    $return .= baseadmin\BaseadminXhtml::ButtonGet("admin\AdminWbTool::Zarchive wb={$inWb} tool={$inToolTable}", basexhtml\BasexhtmlIcon::Get('fa-archive'), ' title="Archive"', 'xsmall', 'Move to zarchive?');
                    break;
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    private static function __InlineBlockGet($inString, $inButtons, $inType = '', $inExists = false): string {
        $style = 'display:inline-block; min-width:130px; margin:0 5px 5px 0; padding-left:5px;';
        switch ($inType) {
            case '1-mandatory':
                $style .= ' border: 1px solid #ccc; background: #eee;';
                break;
            case '1-optional':
                $style .= ' border: 1px solid #cc4; background: #eee;';
                break;
            case '1-additional':
                $style .= ' border: 1px solid #48c; background: #eee;';
                break;
            case '0-mandatory':
                $style .= ' border: 1px solid #c44;';
                break;
            case '0-optional':
                $style .= ' border: 1px solid #cc4;';
                break;
        }
        $buttons = (empty($inButtons)) ? '' : "<span style='font-size:0.8em'>$inButtons</span>";
        $return = "<div style=' $style'>{$inString}<br>{$buttons}</div>";
        return $return;
    }
    /* -------------------------------------------------------------------- */
}