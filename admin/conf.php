<?php
use workbook\wbinc\admin;
use workbook\wbinc\doku;
use workbookcore\wbinc\sys;
class admin_plugin_workbook_conf extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_conf');
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_conf.svg';
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
        // Content
        if (strpos(@constant('WB_RUNMODE'), 'workbookcore-ok') === false) {
            echo admin\AdminXhtml::TextInstallLinkGet();
        } else {
            echo('<h2>Conf</h2>');
            // Form
            echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuGlobal::NsidGet()) . '" method="post">');
            echo('  <input type="hidden" name="do"   value="admin" />');
            echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_conf" />');
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            $rows = $this->__ArraySetupGet();
            $styles = ['width:150px;', '', 'width:150px;text-align:center;', 'width:150px;text-align:center;', 'width:100px;text-align:center;'];
            echo admin\AdminXhtml::TableGet($rows, $styles);
            // Form
            echo('</form>');
        }
    }
    /* -------------------------------------------------------------------- */
    private function __ArraySetupGet() {
        $attr = (sys\SysRemote::EnabledCheck()) ? '' : 'disabled';
        $returns = [];
        $returns[] = ['TH:CONF', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Marketplace'];
        $returns[] = ['Marketplace Conf', 'Checks the remote marketplace configurations.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Remote action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Remote action=login', 'login', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ["Marketplace Sync", "Clears the synced content (zsync/sync) and resyncs with the marketplace.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbSyncExec action=status scope=zsync/sync', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSyncExec action=clear scope=zsync/sync', 'clear', $attr), ''];
        $returns[] = ['TH:Dokuwiki'];
        $returns[] = ['Plugins', 'Checks installed plugins.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Plugins action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Plugins action=check', 'check'), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        $returns[] = ['Templates', 'Checks installed templates.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Tpls action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Tpls action=check', 'check'), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        $returns[] = ['Confs', 'Checks the main configurations.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Confs action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Confs action=check', 'check'), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ['Confs-protected', 'Changes conf/local.protected.php.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::LocalProtected action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=config')];
        $returns[] = ['ACL', 'Changes conf/acl.auth.php.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acl action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=acl')];
        $returns[] = ['Entities', 'Changes conf/entities.local.conf (for look&feel).', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Entities action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Acronyms', 'Changes conf/acronyms.local.conf (for look&feel).', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acronyms action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Interwiki', 'Changes conf/interwiki.local.conf (for links).', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Interwiki action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Mime', 'Changes conf/mime.local.conf (for media).', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Mime action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=replace', 'replace'), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['License', 'Changes conf/license.local.php (for licenses).', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::License action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=append', 'append') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=replace', 'replace'), 'cli'];
        $returns[] = ['Links', 'Links conf/lang/, conf/plugin/ and conf/plugin_lang/.', admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Link action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=relink', 'relink') . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=remove', 'remove'), 'cli'];
        // $returns[] = ['TH:Data'];
        // foreach (util\UtilPath::Scandir('lib/plugins/workbookcore/wbconf/data-pages-home') as $val) {
        //     $returns[] = [$val, 'Replaces file in "home".', admin\AdminCmd::ExecGet("admincore\AdmincoreConf::Home action=status id=$val", 'status'), admin\AdminXhtml::ButtonGet("admincore\AdmincoreConf::Home action=replace id=$val", 'replace'), admin\AdminXhtml::LinkGet("?id=" . strtr($val, ['.txt' => '']))];
        // }
        $returns[] = ['TH:Workbooks'];
        $ar = sys\SysNs::ScandirAr(':', 'local', 'pages', 'dirs');
        $count = count($ar);
        $returns[] = ['Workbooks', 'Creates and manages the content of workbooks.', "$count installed", admin\AdminXhtml::LinkGet('?do=admin&page=workbook_marketplace'), admin\AdminXhtml::LinkGet('?do=admin&page=workbook_workbook')];
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}