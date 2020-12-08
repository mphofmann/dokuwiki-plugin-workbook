<?php
use workbook\wbinc\admin;
use workbook\wbinc\dokuadmin;
class admin_plugin_workbook_install extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'install';
    /* -------------------------------------------------------------------- */
    protected function _Array1Get() {
        $returns = [];
        $returns[] = ['TH:INSTALL', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        foreach (dokuadmin\DokuadminInfra::$Infras as $id => $val) {
            $strbtn = '';
            if (strpos("PHP Dokuwiki", $id) !== false) $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInfra::Action action=version id=$id", 'version');
            $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInfra::Action action=info id=$id", 'info');
            $strlink = $id == 'Dokuwiki' ? admin\AdminXhtml::LinkGet('?do=admin') : 'cli';
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInfra::Action action=note id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInfra::Action action=status id=$id"), $strbtn, $strlink];
        }
        $returns[] = ['TH:Plugins'];
        foreach (dokuadmin\DokuadminExtension::$Extensions['plugins'] as $id => $val) {
            $strbtn = admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminExtension::Action action=install type=plugins id=$id", 'install');
            if (strpos(dokuadmin\DokuadminExtension::$Extensions['plugins'][$id], 'manual:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins');
            if (strpos(dokuadmin\DokuadminExtension::$Extensions['plugins'][$id], 'deb:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=workbook_connect');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=note type=plugins id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=status type=plugins id=$id"), $strbtn, admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        };
        $returns[] = ['TH:Templates'];
        foreach (dokuadmin\DokuadminExtension::$Extensions['templates'] as $id => $val) {
            $strbtn = admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=install type=templates id=$id");
            if (strpos(dokuadmin\DokuadminExtension::$Extensions['plugins'][$id], 'deb:') === 0) $strbtn = admin\AdminXhtml::LinkGet('?do=admin&page=workbook_connect');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=note type=templates id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=status type=templates id=$id"), $strbtn, admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        };
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return true;
    }
    /* -------------------------------------------------------------------- */
}