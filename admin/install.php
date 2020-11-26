<?php
use workbook\wbinc\admin;
use workbook\wbinc\doku;
use workbook\wbinc\dokuadmin;
class admin_plugin_workbook_install extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_install.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_install');
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
        echo('<h2>Install</h2>');
        // Form
        echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuSysGlobal::NsidGet()) . '" method="post">');
        echo('  <input type="hidden" name="do"   value="admin" />');
        echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_install" />');
        doku\DokuXhtmlForm::SecTokenEcho();
        // Table
        $rows = $this->__ArrayInstallGet();
        $strwidth = 'width:130px;white-space:nowrap;';
        $strstyle = 'text-align:center;white-space:nowrap;';
        $styles = [$strwidth, $strwidth, $strwidth . $strstyle, $strwidth . $strstyle, $strwidth . $strstyle];
        echo admin\AdminXhtml::TableGet($rows, $styles);
        // Form
        echo('</form>');
    }
    /* -------------------------------------------------------------------- */
    private function __ArrayInstallGet() {
        $returns = [];
        $returns[] = ['TH:INSTALL', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Infra'];
        foreach (dokuadmin\DokuadminInstall::$Infras as $id => $val) {
            $strbtn = '';
            if (strpos("PHP Dokuwiki", $id) !== false) $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Infra action=version id=$id", 'version');
            $strbtn .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Infra action=info id=$id", 'info');
            $strlink = $id == 'Dokuwiki' ? admin\AdminXhtml::LinkGet('?do=admin') : 'cli';
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Infra action=note id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Infra action=status id=$id"), $strbtn, $strlink];
        };
        $returns[] = ['TH:Plugins'];
        foreach (dokuadmin\DokuAdminInstall::$Extensions['plugins'] as $id => $val) {
            $strbtn = admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Extension action=install type=plugins id=$id", 'install');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=note type=plugins id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=status type=plugins id=$id"), $strbtn, admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        };
        $returns[] = ['TH:Templates'];
        foreach (dokuadmin\DokuAdminInstall::$Extensions['templates'] as $id => $val) {
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=note type=templates id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=status type=templates id=$id"), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Extension action=install type=templates id=$id", 'install'), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        };
        $returns[] = ['TH:Webroot'];
        foreach (dokuadmin\DokuAdminInstall::$Webroots as $id => $val) {
            $strlink = '';
            if (strpos('index.php wb.php', $id) !== false) $strlink .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Webroot action=install id=$id", 'install');
            if ('index.php' == $id) $strlink .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Webroot action=restore id=$id", 'restore', (file_exists("$id.orig")) ? '' : 'disabled');
            if ('wb.php' == $id) $strlink .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Webroot action=remove id=$id", 'remove', (file_exists($id)) ? '' : 'disabled');
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Webroot action=note id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Webroot action=status id=$id"), $strlink, 'cli'];
        }
        $returns[] = ['TH:Confs'];
        foreach (dokuadmin\DokuAdminInstall::$Confs as $id => $val) {
            $strlink = admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Conf action=install id=$id", 'install');
            $attr = (file_exists("conf/$id.orig")) ? '' : 'disabled';
            $strlink .= admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Conf action=restore id=$id", 'restore', $attr);
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Conf action=note id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Conf action=status id=$id"), $strlink, 'cli'];
        }
        $returns[] = ['TH:Workbook Plugins'];
        if (strpos(WB_RUNMODE, 'ioncube-ok') === false) {
            $returns[] = ['Warning', 'Ioncube Loader not installed ... skipping.', '', '', ''];
        } else {
            foreach (dokuadmin\DokuAdminInstall::$WbExtensions['plugins'] as $id => $val) {
                $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=note type=plugins id=$id"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::Extension action=status type=plugins id=$id"), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::Extension action=install type=plugins id=$id", 'install'), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
            }
        }
        $returns[] = ['TH:ALL', 'TH:', 'TH:', 'TH:' . admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::All action=install", 'install all', '', '', 'OK?'), 'TH:'];
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}