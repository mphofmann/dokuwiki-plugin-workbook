<?php
namespace workbook\wbdef\mainadmin;
use workbook\wbinc\baseadmin;
class MainadminInfra extends a_Mainadmin {
    /* -------------------------------------------------------------------- */
    protected static $_AdminOnly = true;
    protected static $_Icon = 'fas fa-server';
    protected static $_Item = 'infra';
    protected static $_Note = 'Verifies and installs infrastructure components.';
    private static $__ConnectedCheck = false;
    private static $__SystemsAr = [];
    private static $__WorkbookcoreCheck = false;
    /* -------------------------------------------------------------------- */
    public static function A_Construct(): bool {
        self::$__ConnectedCheck = baseadmin\BaseadminRemote::EnabledCheck();
        if (self::$__ConnectedCheck) self::$__SystemsAr = baseadmin\BaseadminRemote::SystemsAr();
        self::$__WorkbookcoreCheck = is_dir('workbook/module/workbookcore');
        return true;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array01Get(): array {
        $returns = [];
        $attr = self::$__ConnectedCheck ? '' : 'disabled';
        $returns[] = ['TH:DEPENDS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        $returns[] = baseadmin\BaseadminInfra::RowAr('Infra');
        $returns[] = baseadmin\BaseadminInfra::RowAr('PHP');
        $returns[] = baseadmin\BaseadminInfra::RowAr('PHP-Ioncube');
        $returns[] = baseadmin\BaseadminInfra::RowAr('Webroot');
        $returns[] = ['TH:Marketplace'];
        $returns[] = ['Marketplace', baseadmin\BaseadminRemote::NoteGet(), baseadmin\BaseadminCmd::ExecGet('baseadmin\BaseadminRemote::Exec action=status', 'status'), baseadmin\BaseadminXhtml::ButtonGet('baseadmin\BaseadminRemote::Exec action=login', '[Login]', $attr), baseadmin\BaseadminXhtml::LinkGet('?do=admin&wb_main=conf&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ['Cache', 'Marks cache as purged.', baseadmin\BaseadminCmd::ExecGet('baseadmin\BaseadminInfra::ConfLocalExec action=status', 'status'), baseadmin\BaseadminXhtml::ButtonGet('baseadmin\BaseadminInfra::ConfLocalExec action=purge', '[Purge]'), ''];
        $returns[] = ['TH:Modules'];
        foreach (self::$__SystemsAr["depends-module"] ?? [] as $id => $ar) {
            $strstatus = baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminExtension::Exec action=info type=module id=$id", baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminExtension::Exec action=status type=module id=$id"));
            $cmd = is_dir("workbook/module/$id") ? 'Replace' : 'Install';
            $strbtn = baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminExtension::Exec action=" . strtolower($cmd) . " type=module id=$id", "[$cmd]");
            $returns[] = [$id, baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminExtension::Exec action=note type=module id=$id"), $strstatus, $strbtn, baseadmin\BaseadminXhtml::LinkGet('wb.php?do=admin&wb_main=module')];
        }
        $returns[] = ['TH:Webroots'];
        if (self::$__ConnectedCheck) {
            $attr = self::$__ConnectedCheck * self::$__WorkbookcoreCheck ? '' : 'disabled';
            $returns[] = ['wb.php', 'Worbook controller', $attr == 'disabled' ? baseadmin\BaseadminXhtml::StatusGet('white') : baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminWebroot::Exec action=status id=wb.php"), baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=link id=wb.php", '[Link]', $attr) . baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=unlink id=wb.php", '[Unlink]', (file_exists('wb.php')) ? '' : 'disabled'), ''];
            $returns[] = ['index.php', 'Controller switcher', $attr == 'disabled' ? baseadmin\BaseadminXhtml::StatusGet('white') : baseadmin\BaseadminCmd::ExecGet('baseadmin\BaseadminWebroot::Exec action=status id=index.php', 'status'), baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=link id=index.php", '[Link]', $attr) . baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=unlink id=index.php", '[Unlink]', (file_exists("index.php.orig")) ? '' : 'disabled'), ''];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array02Get(): array {
        if ( ! \_Wb_::RunarchCheck('doku')) return [];
        $returns = [];
        $returns[] = ['TH:RECOMMENDS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        $returns[] = baseadmin\BaseadminInfra::RowAr('Dokuwiki');
        $returns[] = ['TH:Webroots'];
        if (self::$__ConnectedCheck) {
            $attr = self::$__ConnectedCheck * self::$__WorkbookcoreCheck ? '' : 'disabled';
            $returns[] = ['doku.php', 'Doku controller', baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminWebroot::Exec action=status id=doku.php"), '', ''];
            $returns[] = ['dokumodal.php', 'Dokumodal controller', $attr == 'disabled' ? baseadmin\BaseadminXhtml::StatusGet('white') : baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminWebroot::Exec action=status id=dokumodal.php"), baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=link id=dokumodal.php", '[Link]', $attr) . baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminWebroot::Exec action=unlink id=dokumodal.php", '[Unlink]', (file_exists('dokumodal.php')) ? '' : 'disabled'), ''];
        }
        $returns = array_merge($returns, self::__RowsAr('plugin', 'recommends'));
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected static function _Array03Get(): array {
        if ( ! \_Wb_::RunarchCheck('doku')) return [];
        $returns = [];
        $returns[] = ['TH:SUGGESTS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns = array_merge($returns, self::__RowsAr('template', 'suggests'));
        $returns = array_merge($returns, self::__RowsAr('plugin', 'suggests'));
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    private static function __RowsAr($inExttype, $inGroup): array {
        $returns = [];
        $returns[] = ['TH:' . ucfirst($inExttype) . 's'];
        foreach (self::$__SystemsAr["$inExttype-$inGroup"] ?? [] as $id => $ar) {
            $strstatus = baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminExtension::Exec action=info type=$inExttype id=$id", baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminExtension::Exec action=status type=$inExttype id=$id"));
            $cmd = (($inExttype == 'plugin' and is_dir("lib/plugins/$id")) or ($inExttype == 'template' and is_dir("lib/tpl/$id"))) ? 'Replace' : 'Install';
            $strbtn = baseadmin\BaseadminXhtml::ButtonGet("baseadmin\BaseadminExtension::Exec action=" . strtolower($cmd) . " type=$inExttype id=$id", "[$cmd]");
            if (strpos($ar['src'], 'manual:') === 0) $strbtn = baseadmin\BaseadminXhtml::LinkGet("doku.php?do=admin&page=extension&tab={$inExttype}s");
            if (strpos($ar['src'], 'deb:') === 0) $strbtn = baseadmin\BaseadminXhtml::LinkGet('doku.php?do=admin&page=workbook_connect');
            $returns[] = [$id, baseadmin\BaseadminCmd::ExecGet("baseadmin\BaseadminExtension::Exec action=note type=$inExttype id=$id"), $strstatus, $strbtn, baseadmin\BaseadminXhtml::LinkGet("doku.php?do=admin&page=extension&tab={$inExttype}s")];
        };
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}