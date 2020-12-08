<?php
use workbook\wbinc\admin;
class admin_plugin_workbook_operating extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'operating';
    /* -------------------------------------------------------------------- */
    protected function _ArrayGet() {
        $returns = [];
        $returns[] = ['TH:OPERATING', 'TH:Note', 'TH:Status', 'TH:Exec', 'TH:Manage'];
        $returns[] = ['TH:Cache'];
        // $returns[] = ['Cache', 'Clears/Purges both caches and resync with the marketplace.', '', admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::CacheExec action=reset', 'reset'), ''];
        $returns[] = ['Dokuwiki Cache', 'Marks Dokuwiki cache as expired.', admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::DokuCacheExec action=status', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::DokuCacheExec action=purge', 'purge'), ''];
        $returns[] = ["Marketplace Sync zsync", "Clears the synced content (zsync:sync) and resyncs with the marketplace thereafter.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbSyncExec action=status ns=zsync:sync', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSyncExec action=clear ns=zsync:sync', 'clear', '', '', 'OK?'), ''];
        $returns[] = ["Marketplace Sync *", "Allows to clear all synced content and resyncs with the marketplace thereafter.", '', '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_workbook')];
        $returns[] = ['TH:Confs'];
        $returns[] = ["Workbook Basics", "Checks the basics.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=basic', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        $returns[] = ["Workbook Confs", "Checks all config files.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=conf', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        // $returns[] = ["Workbook Home", "Checks all files in 'home'.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbConfExec action=status scope=home-files', 'status'), '', admin\AdminXhtml::LinkGet('?do=admin&page=workbook_conf')];
        $returns[] = ['TH:Troubleshooting'];
        // $returns[] = ["Workbook Search", "Search and replace in Workbook pages.", '', admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSearchExec action=form', 'search', $attr), ''];
        $returns[] = ['Dokuwiki Cache', 'Clears all Dokuwiki cache.', admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::DokuCacheExec action=status scope=/', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::DokuCacheExec action=clear', 'clear'), ''];
        $returns[] = ["Workbook Sync", "Clears all Workbook sync.", admin\AdminCmd::ExecGet('admincore\AdmincoreOperating::WbSyncExec action=status scope=/', 'status'), admin\AdminXhtml::ButtonGet('admincore\AdmincoreOperating::WbSyncExec action=clear', 'clear', ''), ''];
        if (auth_isadmin()) {
            $returns[] = ['TH:Periodic (admin)'];
            $returns[] = ['Dokuwiki Search-Index', 'Rebuilds Dokuwiki search index.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=searchindex')];
            $returns[] = ['Dokuwiki Cleanup', 'Cleanups Dokuwiki.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=cleanup')];
            $returns[] = ['Dokuwiki Upgrade', 'Upgrades Dokuwiki.', '', '', admin\AdminXhtml::LinkGet('?do=admin&page=upgrade')];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}