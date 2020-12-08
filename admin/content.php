<?php
use workbook\wbinc\admin;
use workbookcore\wbinc\env;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
class admin_plugin_workbook_content extends workbook\admin\a_adminpage {
    /* -------------------------------------------------------------------- */
    protected $_Page = 'content';
    /* -------------------------------------------------------------------- */
    protected function _StylesAr() {
        $strstyle = 'width:130px; text-align:center; white-space:nowrap;';
        return ['width:130px; white-space:nowrap;', $strstyle, $strstyle, $strstyle, $strstyle, $strstyle, $strstyle];
    }
    /* -------------------------------------------------------------------- */
    protected function _Array1Get() {
        $returns = [];
        $returns[] = ['TH:WORKBOOK', 'TH:start (&lt;wb/&gt; only)', 'TH:Links', 'TH:Trash', 'TH:Download'];
        $returns[] = ['', 'Checks start.txt', 'Check links', 'Clear trash', admin\AdminXhtml::LinkGet("?do=media&ns=user:uprivate:" . env\EnvUserCurrent::Get() . "_uu", 'Download &raquo;&raquo;&raquo;')];
        foreach (sys\SysNs::ScandirAr(':', 'local', 'pages', 'dirs hidepages') as $wb) {
            $wb = (substr($wb, -1) == ':') ? substr($wb, 0, -1) : $wb;
            $strid = "<a href='?id=$wb:start'><b><i class='fa " . mod\ModWb::FieldGet($wb, 'icon') . "' ></i> " . strtoupper($wb) . "</b></a>";
            $strstart = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::StartWbonlyExec action=remove ns={$wb}", 'remove', '', 'small') . ' ' . admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::StartWbonlyExec action=reset ns={$wb}", 'reset', '', 'small');
            $strlink = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::LinkExec action=verify ns={$wb}", 'verify', '', 'small');
            $strtrash = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::ZTrashExec action=clear ns={$wb}", 'clear', '', 'small') . ' ' . admin\AdminCmd::ExecGet("admincore\AdmincoreContent::ZTrashExec action=size ns={$wb}", 'size');
            $strdownload = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::DownloadExec action=create ns={$wb}", 'create', '', 'small');
            $returns[] = [$strid, $strstart, $strlink, $strtrash, $strdownload];
        }
        if (count($returns) > 1) {
            $strid = 'ALL';
            $strstart = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::StartWbonlyExec action=remove", 'remove', '', 'small') . ' ' . admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::StartWbonlyExec action=verify", 'verify', '', 'small');
            $strlink = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::LinkExec action=verify", 'verify', '', 'small');
            $strtrash = '';
            $strdownload = admin\AdminXhtml::ButtonGet("admincore\AdmincoreContent::DownloadExec action=create", 'create', '', 'small');
            $returns[] = [$strid, $strstart, $strlink, $strtrash, $strdownload];
        }
        return $returns;
    }
    /* -------------------------------------------------------------------- */
}