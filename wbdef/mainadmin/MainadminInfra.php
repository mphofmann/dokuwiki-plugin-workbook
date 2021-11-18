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
        $returns[] = ['Marketplace', baseadmin\BaseadminRemote::NoteGet(), baseadmin\BaseadminCmd::ExecGet('baseadmin\BaseadminRemote::Exec action=status', 'status'), baseadmin\BaseadminXhtml::ButtonGet('baseadmin\BaseadminRemote::Exec action=login', '[Login]', $attr), baseadmin\BaseadminXhtml::LinkGet('?wb_job=admin&wb_main=conf&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ['Cache', 'Marks cache as expired.', baseadmin\BaseadminCmd::ExecGet('baseadmin\BaseadminInfra::ConfLocalExec action=status', 'status'), baseadmin\BaseadminXhtml::ButtonGet('baseadmin\BaseadminInfra::ConfLocalExec action=purge', '[Purge]'), ''];
        $returns[] = ['TH:Modules'];
        $returns = array_merge($returns, baseadmin\BaseadminExtension::RowsAr('module', 'depends', '', self::$__SystemsAr["module-depends"] ?? []));
        $returns[] = ['TH:Webroots'];
        if (self::$__ConnectedCheck) {
            $attr = self::$__ConnectedCheck * self::$__WorkbookcoreCheck ? '' : 'disabled';
            $returns[] = baseadmin\BaseadminWebroot::RowAr('wb.php', 'Workbook controller', $attr);
            $returns[] = baseadmin\BaseadminWebroot::RowAr('index.php', 'Controller switcher', $attr);
            $returns[] = baseadmin\BaseadminWebroot::RowAr('dokuiframe.php', 'Dokuiframe controller', (file_exists('doku.php') ? $attr : 'disabled'));
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}