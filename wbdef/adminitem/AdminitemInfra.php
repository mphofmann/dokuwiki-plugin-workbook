<?php
namespace workbook\wbdef\adminitem;
use workbook\wbinc\admin;
class AdminitemInfra extends a_Adminitem {
    /* -------------------------------------------------------------------- */
    protected static $_Item = 'infra';
    protected static $_Icon = 'fas fa-server';
    protected static $_Note = 'Verifies and installs infrastructure components.';
    protected static $_AdminOnly = true;
    private static $__ConnectedCheck = false;
    private static $__WorkbookcoreCheck = false;
    private static $__SystemsAr = [];
    /* -------------------------------------------------------------------- */
    public static function __constructStatic(): bool {
        self::$__ConnectedCheck = admin\AdminRemote::EnabledCheck();
        if (self::$__ConnectedCheck) self::$__SystemsAr = admin\AdminRemote::SystemsAr();
        self::$__WorkbookcoreCheck = is_dir('workbook/module/workbookcore');
        return true;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array1Get(): array {
        $returns = [];
        $attr = self::$__ConnectedCheck ? '' : 'disabled';
        $returns[] = ['TH:DEPENDS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        $returns[] = admin\AdminInfra::RowAr('Infra');
        $returns[] = admin\AdminInfra::RowAr('PHP');
        $returns[] = admin\AdminInfra::RowAr('PHP-Ioncube');
        $returns[] = admin\AdminInfra::RowAr('Webroot');
        $returns[] = ['TH:Marketplace'];
        $returns[] = ['Marketplace', admin\AdminRemote::NoteGet(), admin\AdminCmd::ExecGet('admin\AdminRemote::Exec action=status', 'status'), admin\AdminXhtml::ButtonGet('admin\AdminRemote::Exec action=login', '[Login]', $attr), admin\AdminXhtml::LinkGet('?do=admin&wb_item=config&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ['Cache', 'Marks cache as purged.', admin\AdminCmd::ExecGet('admin\AdminInfra::ConfLocalExec action=status', 'status'), admin\AdminXhtml::ButtonGet('admin\AdminInfra::ConfLocalExec action=purge', '[Purge]'), ''];
        $returns[] = ['TH:Modules'];
        foreach (self::$__SystemsAr["depends-module"] ?? [] as $id => $ar) {
            $strstatus = admin\AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=info type=module id=$id", admin\AdminCmd::ExecGet("admin\AdminExtension::Exec action=status type=module id=$id"));
            $cmd = is_dir("workbook/module/$id") ? 'Replace' : 'Install';
            $strbtn = admin\AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=" . strtolower($cmd) . " type=module id=$id", "[$cmd]");
            $returns[] = [$id, admin\AdminCmd::ExecGet("admin\AdminExtension::Exec action=note type=module id=$id"), $strstatus, $strbtn, admin\AdminXhtml::LinkGet('?do=admin&wb_item=module')];
        }
        $returns[] = ['TH:Webroots'];
        if (self::$__ConnectedCheck) {
            $attr = self::$__ConnectedCheck * self::$__WorkbookcoreCheck ? '' : 'disabled';
            $returns[] = ['wb.php', 'Worbook controller', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet("admin\AdminWebroot::Exec action=status id=wb.php"), admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=link id=wb.php", '[Link]', $attr) . admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=unlink id=wb.php", '[Unlink]', (file_exists('wb.php')) ? '' : 'disabled'), ''];
            $returns[] = ['index.php', 'Controller switcher', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admin\AdminWebroot::Exec action=status id=index.php', 'status'), admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=link id=index.php", '[Link]', $attr) . admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=unlink id=index.php", '[Unlink]', (file_exists("index.php.orig")) ? '' : 'disabled'), ''];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array2Get(): array {
        if ( ! \_Wb_::RunarchCheck('doku')) return [];
        $returns = [];
        $returns[] = ['TH:RECOMMENDS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        $returns[] = admin\AdminInfra::RowAr('Dokuwiki');
        $returns[] = ['TH:Webroots'];
        if (self::$__ConnectedCheck) {
            $attr = self::$__ConnectedCheck * self::$__WorkbookcoreCheck ? '' : 'disabled';
            $returns[] = ['doku.php', 'Doku controller', admin\AdminCmd::ExecGet("admin\AdminWebroot::Exec action=status id=doku.php"), '', ''];
            $returns[] = ['dokumodal.php', 'Dokumodal controller', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet("admin\AdminWebroot::Exec action=status id=dokumodal.php"), admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=link id=dokumodal.php", '[Link]', $attr) . admin\AdminXhtml::ButtonGet("admin\AdminWebroot::Exec action=unlink id=dokumodal.php", '[Unlink]', (file_exists('dokumodal.php')) ? '' : 'disabled'), ''];
        }
        $returns = array_merge($returns, self::__RowsAr('recommends', 'plugin'));
        $returns = array_merge($returns, self::__RowsAr('recommends', 'template'));
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array3Get(): array {
        if ( ! \_Wb_::RunarchCheck('doku')) return [];
        $returns = [];
        $returns[] = ['TH:SUGGESTS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns = array_merge($returns, self::__RowsAr('suggests', 'plugin'));
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __RowsAr($inGroup, $inExttype): array {
        $returns = [];
        $returns[] = ['TH:' . ucfirst($inExttype) . 's'];
        foreach (self::$__SystemsAr["$inGroup-$inExttype"] ?? [] as $id => $ar) {
            $strstatus = admin\AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=info type=$inExttype id=$id", admin\AdminCmd::ExecGet("admin\AdminExtension::Exec action=status type=$inExttype id=$id"));
            $cmd = (($inExttype == 'plugin' and is_dir("lib/plugins/$id")) or ($inExttype == 'template' and is_dir("lib/tpl/$id"))) ? 'Replace' : 'Install';
            $strbtn = admin\AdminXhtml::ButtonGet("admin\AdminExtension::Exec action=" . strtolower($cmd) . " type=$inExttype id=$id", "[$cmd]");
            if (strpos($ar['src'], 'manual:') === 0) $strbtn = admin\AdminXhtml::LinkGet("doku.php?do=admin&page=extension&tab={$inExttype}s");
            if (strpos($ar['src'], 'deb:') === 0) $strbtn = admin\AdminXhtml::LinkGet('doku.php?do=admin&page=workbook_connect');
            $returns[] = [$id, admin\AdminCmd::ExecGet("admin\AdminExtension::Exec action=note type=$inExttype id=$id"), $strstatus, $strbtn, admin\AdminXhtml::LinkGet("doku.php?do=admin&page=extension&tab={$inExttype}s")];
        };
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}