<?php
use workbook\wbinc\admin;
use workbook\wbinc\dokuadmin;
class admin_plugin_workbook_install extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'install';
    /* -------------------------------------------------------------------- */
    protected function _ArrayGet() {
        $returns = [];
        $returns[] = ['TH:INSTALL', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        foreach (dokuadmin\DokuadminInstall::$Infras as $id => $val) {
            $strbtn = '';
            if (strpos("PHP Dokuwiki", $id) !== false) $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::InfraAction action=version id=$id", 'version');
            $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::InfraAction action=info id=$id", 'info');
            $strlink = $id == 'Dokuwiki' ? admin\AdminXhtml::LinkGet('?do=admin') : 'cli';
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::InfraAction action=note id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::InfraAction action=status id=$id"), $strbtn, $strlink];
        };
        $returns[] = ['TH:Plugins'];
        foreach (dokuadmin\DokuAdminInstall::$Extensions['plugins'] as $id => $val) {
            $strbtn = admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::ExtensionAction action=install type=plugins id=$id", 'install');
            if (strpos(dokuadmin\DokuAdminInstall::$Extensions['plugins'][$id], 'manual:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins');
            if (strpos(dokuadmin\DokuAdminInstall::$Extensions['plugins'][$id], 'deb:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=workbook_connect');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=note type=plugins id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=status type=plugins id=$id"), $strbtn, admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        };
        $returns[] = ['TH:Templates'];
        foreach (dokuadmin\DokuAdminInstall::$Extensions['templates'] as $id => $val) {
            $strbtn = admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=install type=templates id=$id");
            if (strpos(dokuadmin\DokuAdminInstall::$Extensions['plugins'][$id], 'deb:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=workbook_connect');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=note type=templates id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=status type=templates id=$id"), $strbtn, admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        };
        $returns[] = ['TH:Webroot'];
        foreach (dokuadmin\DokuAdminInstall::$Webroots as $id => $val) {
            $strbtn = strpos("index.php wb.php", $id) === false ? '' : admin\AdminXhtml::LinkGet('?do=admin&page=workbook_connect');
            $returns[] = [$id, $val, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::WebrootAction action=status id=$id"), $strbtn, 'cli'];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return true;
    }
    /* -------------------------------------------------------------------- */
}