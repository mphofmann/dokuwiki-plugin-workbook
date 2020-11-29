<?php
use workbook\wbinc\doku;
use workbookcore\wbinc\env;
use workbookcore\wbinc\mod;
use workbookcore\wbinc\sys;
use workbook\wbinc\admin;
class admin_plugin_workbook_content extends DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    private $__Out = '';
    private $__Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_content');
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_content.svg';
    }
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
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
        if (strpos(@constant('WB_RUNMODE'), 'workbookcore-ok') === false) {
            echo admin\AdminXhtml::TextInstallLinkGet();
        } else {
            echo('<h2>Content</h2>');
            // Form
            echo('<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuGlobal::NsidGet()) . '" method="post">');
            echo('  <input type="hidden" name="do"   value="admin" />');
            echo('  <input type="hidden" name="page" value="' . $this->getPluginName() . '_content" />');
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            $rows = $this->__ArraySetupGet();
            $strstyle = 'width:130px; text-align:center; white-space:nowrap;';
            $styles = ['width:130px; white-space:nowrap;', $strstyle, $strstyle, $strstyle, $strstyle, $strstyle, $strstyle];
            echo admin\AdminXhtml::TableGet($rows, $styles);
            // Form
            echo('</form>');
        }
    }
    /* -------------------------------------------------------------------- */
    private function __ArraySetupGet() {
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