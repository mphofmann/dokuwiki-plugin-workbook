<?php
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class admin_plugin_workbook_operating extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_operating.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_operating');
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
        echo(admin\AdminXhtml::MenuGet(false));
        // Output from handle
        admin\AdminExec::OutputEcho($this->__Out);
        if (strpos(@constant('WB_RUNMODE'), 'workbookcore-ok') === false) {
            echo admin\AdminXhtml::TextInstallLinkGet();
        } else {
            echo('<h2>Operating</h2>');
            // Form
            echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuGlobal::NsidGet()) . '" method="post">');
            echo('  <input type="hidden" name="do"   value="admin" />');
            echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_operating" />');
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            $rows = $this->__ArrayOperatingGet();
            $styles = ['width:170px;', '', 'width:150px;text-align:center;', 'width:120px;text-align:center;', 'width:100px;text-align:center;'];
            echo admin\AdminXhtml::TableGet($rows, $styles);
            // Form
            echo('</form>');
        }
    }
    /* -------------------------------------------------------------------- */
    private function __ArrayOperatingGet() {
        $returns = [];
        $returns[] = ['TH:OPERATING', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Cache'];
        // $returns[] = ['Cache', 'Clears/Purges both caches and resync with the marketplace.', '', admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::CacheExec action=reset', 'reset'), ''];
        $returns[] = ['Dokuwiki Cache', 'Marks Dokuwiki cache as expired.', admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::DokuCacheExec action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::DokuCacheExec action=purge', 'purge'), ''];
        $returns[] = ["Marketplace Sync zsync", "Clears the synced content (zsync:sync) and resyncs with the marketplace thereafter.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbSyncExec action=status ns=zsync:sync', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSyncExec action=clear ns=zsync:sync', 'clear'), ''];
        $returns[] = ["Marketplace Sync *", "Allows to clear all synced content and resyncs with the marketplace thereafter.", '', '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_workbook')];
        $returns[] = ['TH:Confs'];
        $returns[] = ["Workbook Basics", "Checks the basics.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=basic', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        $returns[] = ["Workbook Confs", "Checks all config files.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=conf', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        // $returns[] = ["Workbook Home", "Checks all files in 'home'.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=home-files', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        $returns[] = ['TH:Troubleshooting'];
        // $returns[] = ["Workbook Search", "Search and replace in Workbook pages.", '', admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSearchExec action=form', 'search', $attr), ''];
        $returns[] = ['Dokuwiki Cache', 'Clears all Dokuwiki cache.', admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::DokuCacheExec action=status scope=/', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::DokuCacheExec action=clear', 'clear'), ''];
        $returns[] = ["Workbook Sync", "Clears all Workbook sync.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbSyncExec action=status scope=/', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSyncExec action=clear', 'clear', ''), ''];
        if (doku\DokuAcl::AdminIs()) {
            $returns[] = ['TH:Periodic (admin)'];
            $returns[] = ['Dokuwiki Search-Index', 'Rebuilds Dokuwiki search index.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=searchindex')];
            $returns[] = ['Dokuwiki Cleanup', 'Cleanups Dokuwiki.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=cleanup')];
            $returns[] = ['Dokuwiki Upgrade', 'Upgrades Dokuwiki.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=upgrade')];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}