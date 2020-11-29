<?php
use workbook\wbinc\admin;
use workbook\wbinc\admincore;
use workbook\wbinc\doku;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
use workbookcore\wbinc\xhtml;
class admin_plugin_workbook_workbook extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    private static function __InlineBlockGet($inString, $inButtons, $inType = '', $inExists = false) {
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
    private static function __ButtonsGet($inType, $inWb, $inToolTable) {
        $return = '';
        $tooltable = mod\ModTool::TypeGet($inToolTable);
        $cmds = '';
        switch ($inType) {
            case 'tool';
                $cmds = mod\ModWbTool::CommandsGet($inWb, $tooltable);
                break;
            case 'table';
                $cmds = mod\ModWbTable::CommandsGet($inWb, $tooltable);
                break;
        }
        foreach (explode(',', $cmds) as $cmd) {
            if (empty($cmd)) continue;
            if ($cmd == 'install' or $cmd == 'reset') continue;
            switch ($inType) {
                case 'tool':
                    $attr = mod\ModWbTool::CommandEnabledCheck($inWb, $tooltable, $cmd) ? '' : 'disabled';
                    $return .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWbTool::{$cmd} wb={$inWb} tool={$inToolTable}", $cmd, $attr, 'small');
                    break;
                case 'table':
                    $attr = mod\ModWbTable::CommandEnabledCheck($inWb, $tooltable, $cmd) ? '' : 'disabled';
                    $return .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWbTable::{$cmd} wb={$inWb} table={$inToolTable}", $cmd, $attr, 'small');
                    break;
            }
        }
        if (is_dir("data/pages/$inWb/$inToolTable")) {
            switch ($inType) {
                case 'tool';
                    ob_start();
                    admincore\AdmincoreOperating::WbSyncExec('size', "$inWb:$inToolTable");
                    $strsize = ob_get_clean();
                    if (!empty($strsize)) $return .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWbTool::Syncclear wb={$inWb} tool={$inToolTable}", xhtml\XhtmlIcon::Get('fa-undo-alt'), " title='Clear sync ($strsize)'", 'xsmall', 'Clear sync?');
                    $return .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWbTool::Zarchive wb={$inWb} tool={$inToolTable}", xhtml\XhtmlIcon::Get('fa-archive'), ' title="Archive"', 'xsmall', 'Move to zarchive?');
                    break;
            }
        }
        return $return;
    }
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_workbook.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_workbook');
    }
    /* -------------------------------------------------------------------- */
    function handle() {
        $str = admin\AdminExec::HandleExec($this->__Cmds);
        if ($str === false) return;
        $this->__Out = $str;
    }
    /* -------------------------------------------------------------------- */
    function html() {
        // Heading
        echo('<h1>Workbook</h1>');
        echo(admin\AdminXhtml::MenuGet());
        admin\AdminExec::OutputEcho($this->__Out);
        if (strpos(@constant('WB_RUNMODE'), 'workbookcore-ok') === false) {
            echo admin\AdminXhtml::TextInstallLinkGet();
        } else {
            echo('<h2>Workbook</h2>');
            // Form
            echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuGlobal::NsidGet()) . '" method="post">');
            echo('  <input type="hidden" name="do"   value="admin" />');
            echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_workbook" />');
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            $rows = $this->__ArraySetupGet();
            $styles = ['width:130px; white-space:nowrap;', 'width:60px', 'min-width:430px', 'min-width:290px'];
            echo admin\AdminXhtml::TableGet($rows, $styles);
            // Form
            echo('</form>');
        }
    }
    /* -------------------------------------------------------------------- */
    private function __ArraySetupGet() {
        $returns = [];
        $returns[] = ['TH:WORKBOOK', 'TH:Action', 'TH:Tools', 'TH:Database & Datapages'];
        foreach (sys\SysNs::ScandirAr(':', 'local', 'pages', 'dirs hidepages') as $wb) {
            $wb = (substr($wb, -1) == ':') ? substr($wb, 0, -1) : $wb;
            // id
            $strid = "<a href='?id=$wb:start'><b><i class='fa " . mod\ModWb::FieldGet($wb, 'icon') . "' ></i> " . strtoupper($wb) . "</b></a>";
            // workbook
            $cmd = 'reset';
            $attr = mod\ModWb::CommandEnabledCheck($wb, $cmd) ? '' : 'disabled';
            $strwb = admin\AdminXhtml::ButtonGet("admincore\AdmincoreWb::{$cmd} wb={$wb}", $cmd, $attr);
            ob_start();
            admincore\AdmincoreOperating::WbSyncExec('size', $wb);
            $strsize = ob_get_clean();
            if (!empty($strsize)) $strwb .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWb::Syncclear wb={$wb}", xhtml\XhtmlIcon::Get('fa-undo-alt'), " title='Clear sync ($strsize)'", 'xsmall', 'Clear sync?');
            $strwb .= admin\AdminXhtml::ButtonGet("admincore\AdmincoreWb::Zarchive wb={$wb}", xhtml\XhtmlIcon::Get('fa-archive'), ' title="Archive"', 'xsmall', 'Move to zarchive?');
            // tools
            $adds = ['1-mandatory' => '', '1-optional' => '', '1-additional' => '', '0-mandatory' => '', '0-optional' => ''];
            $dirs = sys\SysNs::ScandirAr($wb, 'local', 'pages', 'dirs');
            $ar = admincore\AdmincoreWb::ToolAr($wb);
            ksort($ar, SORT_NATURAL);
            foreach ($ar as $tool => $modes) {
                unset($dirs[array_search("$tool:", $dirs)]);
                $add = "<a style='font-weight:bold;' href='?id={$wb}:{$tool}:start'>" . ucfirst($tool) . "</a> ";
                $btn = self::__ButtonsGet('tool', $wb, $tool);
                $type = (strpos($modes, 'reset') === false) ? 'optional' : 'mandatory';
                if (is_dir("data/pages/$wb/$tool")) {
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
            foreach (admincore\AdmincoreWb::TableAr($wb, 'database') as $table => $fields) {
                $add = "<a style='font-weight:bold;' href='?do=admin&page=struct_schemas&table={$wb}_{$table}'>" . ucfirst($table) . "</a> ";
                $btn = self::__ButtonsGet('table', $wb, $table);
                $type = (sys\SysTable::Exists($wb, $table)) ? '1-mandatory' : '0-mandatory';
                $strdb .= self::__InlineBlockGet($add, $btn, $type);
            }
            // datapages
            $strdp = '';
            foreach (admincore\AdmincoreWb::TableAr($wb, 'datapage') as $table => $fields) {
                $add = "<a style='font-weight:bold;' href='?do=admin&page=struct_schemas&table={$wb}_{$table}'>" . ucfirst($table) . "</a> ";
                $btn = self::__ButtonsGet('table', $wb, $table);
                $type = (sys\SysTable::Exists($wb, $table)) ? '1-mandatory' : '0-mandatory';
                $strdp .= self::__InlineBlockGet($add, $btn, $type);
            }
            // add
            $returns[] = [$strid, $strwb, $strtools, $strdb . $strdp];
        }
        if (count($returns) > 1) {
            $returns[] = ['ALL', admin\AdminXhtml::ButtonGet("admincore\AdmincoreWb::Resets", 'resets'), '', ''];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}