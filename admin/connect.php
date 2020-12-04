<?php
use workbook\wbinc\admin;
use workbook\wbinc\doku;
use workbook\wbinc\dokuadmin;
class admin_plugin_workbook_connect extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'connect';
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return true;
    }
    /* -------------------------------------------------------------------- */
    protected function _ArrayGet() {
        $isconnected = dokuadmin\DokuadminConnect::EnabledCheck();
        $isworkbookcore = is_dir('lib/plugins/workbookcore');
        $attr = $isconnected ? '' : 'disabled';
        $returns = [];
        $returns[] = ['TH:CONNECT', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        // Marketplace
        $returns[] = ['TH:Marketplace'];
        $returns[] = ['Marketplace Conf', "Checks the marketplace configuration:<br>" . $this->__NoteRemoteGet(), admin\AdminCmd::ExecGet('dokuadmin\DokuadminConnect::RemoteAction action=status', 'status'), admin\AdminXhtml::ButtonGet('dokuadmin\DokuadminConnect::RemoteAction action=login', 'login', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        // Plugins & Templates
        $ar = $isconnected ? dokuadmin\DokuadminConnect::SystemsAr() : [];
        foreach (['plugin' => 'Plugins (lib/plugins)', 'template' => 'Templates (lib/tpl)'] as $exttype => $th) {
            $returns[] = ["TH:$th"];
            if (strpos(WB_RUNMODE, 'ioncube-ok') === false) {
                $returns[] = ['Warning', 'Ioncube Loader not installed ... skipping.', '', '', ''];
            } elseif (is_array($ar[$exttype . 's'])) {
                foreach ($ar[$exttype . 's'] as $id => $val) {
                    $val = strtr($val, ['$ID$' => $id]);
                    $url = $val;
                    $tarpath = '';
                    if (substr($val, 0, 4) == 'deb:') {
                        list($ext, $package, $tarpath) = explode(':', $val);
                        $url = "{$ar['*']['deburl']}{$ar['*']['debprefix']}{$package}.{$ext}";
                    }
                    $returns[] = [$id, admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=note type=$exttype id=$id url=$url"), admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::ExtensionAction action=status type=$exttype id=$id url=$url"), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::ExtensionAction action=install type=$exttype id=$id, url=$url tarpath=$tarpath", 'install'), admin\AdminXhtml::LinkGet("?do=admin&page=extension&tab=$exttype")];
                }
            }
        }
        // Webroots
        $returns[] = ['TH:Webroots (/)'];
        $returns[] = ['index.php', 'Controller switcher', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('dokuadmin\DokuadminInstall::WebrootAction action=status id=index.php', 'status'), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::WebrootAction action=link id=index.php", 'link', $attr) . admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::WebrootAction action=restore id=index.php", 'restore', (file_exists("index.php.orig")) ? '' : 'disabled'), 'cli'];
        $returns[] = ['wb.php', 'Worbook controller', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet("dokuadmin\DokuadminInstall::WebrootAction action=status id=wb.php"), admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::WebrootAction action=link id=wb.php", 'link', $attr) . admin\AdminXhtml::ButtonGet("dokuadmin\DokuadminInstall::WebrootAction action=remove id=wb.php", 'remove', (file_exists('wb.php')) ? '' : 'disabled'), 'cli'];
        // Confs
        $returns[] = ['TH:Confs (conf)'];
        $attr = $isconnected * $isworkbookcore ? '' : 'disabled';
        $returns[] = ['Plugins', 'Checks installed plugins.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Plugins action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Plugins action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=plugins')];
        $returns[] = ['Templates', 'Checks installed templates.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Tpls action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Tpls action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=extension&tab=templates')];
        $returns[] = ['Confs', 'Checks the main configurations.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Confs action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Confs action=check', 'check', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config#plugin____workbook____plugin_settings_name')];
        $returns[] = ['Confs-protected', 'Changes conf/local.protected.php.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::LocalProtected action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::LocalProtected action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=config')];
        $returns[] = ['ACL', 'Changes conf/acl.auth.php.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acl action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acl action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=acl')];
        $returns[] = ['Entities', 'Changes conf/entities.local.conf (for look&feel).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Entities action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Entities action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Acronyms', 'Changes conf/acronyms.local.conf (for look&feel).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Acronyms action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Acronyms action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Interwiki', 'Changes conf/interwiki.local.conf (for links).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Interwiki action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Interwiki action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['Mime', 'Changes conf/mime.local.conf (for media).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Mime action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Mime action=replace', 'replace', $attr), admin\AdminXhtml::LinkGet('?do=admin&page=confmanager')];
        $returns[] = ['License', 'Changes conf/license.local.php (for licenses).', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::License action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=append', 'append', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::License action=replace', 'replace', $attr), 'cli'];
        $returns[] = ['Links', 'Links conf/lang/, conf/plugin/ and conf/plugin_lang/.', $attr == 'disabled' ? admin\AdminXhtml::StatusGet('white') : admin\AdminCmd::ExecGet('admincore\AdmincoreConf::Link action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=relink', 'relink', $attr) . admin\AdminXhtml::ButtonGet('admincore\AdmincoreConf::Link action=remove', 'remove', $attr), 'cli'];
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
    private function __NoteRemoteGet() {
        $return = '<table style="border:1px solid #ccc; font-size:smaller;">';
        $url = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_url');
        $username = doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_username');
        $password = str_pad('', strlen(doku\DokuGlobal::ConfGet('plugin', 'workbook', 'connect_password')), '*');
        if(empty($url)) $url = '?';
        if(empty($username)) $username = '?';
        if(empty($password)) $password = '?';
        $myip = file_get_contents("https://ipecho.net/plain");
        $return .= "<tr><td>Url:</td><td>{$url}</td></tr>";
        $return .= "<tr><td>Login:</td><td>{$username} {$password}</td></tr>";
        $return .= "<tr><td>My IP:</td><td>{$myip}</td></tr>";
        $return .= '</table>';
        return $return;
    }
    /* -------------------------------------------------------------------- */
}

