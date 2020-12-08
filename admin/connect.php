<?php
use workbook\wbinc\admin;
use workbook\wbinc\dokuadmin;
class admin_plugin_workbook_connect extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'connect';
    private $__SystemsAr = [];
    private $__ConnectedIs = false;
    private $__WorkbookcoreIs = false;
    /* -------------------------------------------------------------------- */
    public function __construct() {
        $this->__ConnectedIs = dokuadmin\DokuadminConnect::EnabledCheck();
        $this->__WorkboocoreIs = is_dir('lib/plugins/workbookcore');
        if ($this->__ConnectedIs) $this->__SystemsAr = dokuadmin\DokuadminConnect::SystemsAr();
    }
    /* -------------------------------------------------------------------- */
    protected function _Array2Get() {
        $returns = [];
        $returns[] = ['TH:PLUS', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        // Webroots
        $returns[] = ['TH:Webroots (/)'];
        $attr = $this->__ConnectedIs * $this->__WorkbookcoreIs ? '' : 'disabled';
        $returns[] = ['index.php', 'Controller switcher', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('dokuadmin\DokuadminWebroot::Action action=status id=index.php', 'status'), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminWebroot::Action action=link id=index.php", 'link', $attr) . admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminWebroot::Action action=restore id=index.php", 'restore', (file_exists("index.php.orig")) ? '' : 'disabled'), 'cli'];
        $returns[] = ['wb.php', 'Worbook controller', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet("dokuadmin\DokuadminWebroot::Action action=status id=wb.php"), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminWebroot::Action action=link id=wb.php", 'link', $attr) . admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminWebroot::Action action=remove id=wb.php", 'remove', (file_exists('wb.php')) ? '' : 'disabled'), 'cli'];
        // Plugins Premium
        $returns[] = ["TH:Plugins Premium (lib/plugins)"];
        $ar = $this->__SystemsAr;
        if (is_array($ar['plugins-premium'])) {
            $returns = array_merge($returns, $this->__RowsExtGet($ar['plugins-premium'], 'plugin', $ar['*']['deburl'], $ar['*']['debprefix']));
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    protected function _ArrayGet() {
        $this->__ConnectedIs = dokuadmin\DokuadminConnect::EnabledCheck();
        $this->__WorkbookcoreIs = is_dir('lib/plugins/workbookcore');
        $attr = $this->__ConnectedIs ? '' : 'disabled';
        $returns = [];
        $returns[] = ['TH:BASIC', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        // Marketplace
        $returns[] = ['TH:Marketplace'];
        $returns[] = ['Marketplace Conf', "Checks the marketplace configuration:<br>" . dokuadmin\DokuadminConnect::NoteGet(), admin\AdminCmd::ExecGet('dokuadmin\DokuadminConnect::Action action=status', 'status'), admin\AdminXhtml::ButtonGet('dokuadmin\DokuadminConnect::Action action=login', 'login', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        // Plugins & Templates
        $ar = $this->__SystemsAr;
        foreach (['plugin' => 'Plugins (lib/plugins)', 'template' => 'Templates (lib/tpl)'] as $exttype => $th) {
            $returns[] = ["TH:$th"];
            if (is_array($ar[$exttype . 's'])) {
                $returns = array_merge($returns, $this->__RowsExtGet($ar[$exttype . 's'], $exttype, $ar['*']['deburl'], $ar['*']['debprefix']));
            }
        }
        // Confs
        $returns[] = ['TH:Confs (conf)'];
        $returns[] = ['Confs-protected', 'Changes conf/local.protected.php.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::LocalProtected action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config')];
        $returns[] = ['ACL', 'Changes conf/acl.auth.php.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acl action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=acl')];
        $returns[] = ['Entities', 'Changes conf/entities.local.conf (for look&feel).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Entities action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Acronyms', 'Changes conf/acronyms.local.conf (for look&feel).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acronyms action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Interwiki', 'Changes conf/interwiki.local.conf (for links).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Interwiki action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Mime', 'Changes conf/mime.local.conf (for media).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Mime action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=append', 'append', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['License', 'Changes conf/license.local.php (for licenses).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::License action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=replace', 'replace', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=append', 'append', $attr), 'cli'];
        $returns[] = ['Links', 'Links conf/lang/, conf/plugin/ and conf/plugin_lang/.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Link action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=relink', 'relink', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=remove', 'remove', $attr), 'cli'];
        // Checks
        $returns[] = ['TH:Checks'];
        $attr = $this->__ConnectedIs * $this->__WorkbookcoreIs ? '' : 'disabled';
        $returns[] = ['Plugins', 'Checks installed plugins.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Plugins action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Plugins action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        $returns[] = ['Templates', 'Checks installed templates.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Tpls action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Tpls action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        $returns[] = ['Confs', 'Checks the main configurations.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Confs action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Confs action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        // Workbooks
        $returns[] = ['TH:Workbooks (data/pages)'];
        $count = 0;
        foreach (scandir('data/pages') as $inode) {
            if (substr($inode, 0, 1) != '.') $count++;
        }
        $returns[] = ['Workbooks', 'Creates and manages the content of workbooks.', "$count installed", admin\AdminXhtml::LinkGet('?do=admin&page=workbook_marketplace'), admin\AdminXhtml::LinkGet('?do=admin&page=workbook_workbook')];
        return $returns;
    }
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return true;
    }
    /* -------------------------------------------------------------------- */
    private function __RowsExtGet($inAr, $inExttype, $inDebUrl, $inDebPrefix) {
        $returns = [];
        foreach ($inAr as $id => $val) {
            $val = strtr($val, ['$ID$' => $id]);
            if (substr($val, 0, 4) == 'deb:') {
                list($ext, $package, $tarpath) = explode(':', $val);
                $url = "{$inDebUrl}{$inDebPrefix}{$package}.{$ext}";
            } else {
                $url = $val;
                $tarpath = '';
            }
            $attr = (strpos(WB_RUNMODE, 'ioncube-ok') === false and strpos($url, 'ioncube') !== false) ? 'disabled' : '';
            $note = $attr == 'disabled' ? ' <span title="PHP-Ioncube loader is missing." style=" font-weight:bold; color:#c44;cursor:pointer">!!!</span>' : '';
            $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=note type=$inExttype id=$id url=$url"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminExtension::Action action=status type=$inExttype id=$id url=$url") . $note, admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminExtension::Action action=install type=$inExttype id=$id url=$url tarpath=$tarpath", 'install', $attr), admin\AdminXhtml::LinkGet("?do=admin&page=extension&tab=$inExttype")];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}

