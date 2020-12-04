<?php
namespace workbook\admin;
use workbook\wbinc\admin;
use workbook\wbinc\doku;
class a_adminpage extends \DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    protected $_Page = '';
    protected $_Out = '';
    protected $_Cmds = ['cmd' => ''];
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin_' . $this->_Page . '.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuSort() {
        return 7771;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang("menu_" . $this->_Page);
    }
    /* -------------------------------------------------------------------- */
    public function handle() {
        $str = admin\AdminExec::HandleExec($this->_Cmds);
        if ($str === false) return;
        $this->_Out = $str;
    }
    /* -------------------------------------------------------------------- */
    public function html() {
        // Heading
        echo('<h1>Workbook</h1>');
        echo(admin\AdminXhtml::MenuGet());
        admin\AdminExec::OutputEcho($this->_Out);
        echo('<h2><img src="lib/plugins/workbook/admin_' . $this->_Page . '.svg" style="height:0.8em; width:0.8em;" /> ' . ucfirst($this->_Page) . '</h2>');
        if (strpos('install connect', $this->_Page) === false and !is_dir('lib/plugins/workbookcore')) {
            echo "<h3><a href='?do=admin&page=workbook_connect'>&raquo; Installation incomplete &laquo;</a></h3>";
        } else {
            // Form
            echo '<form action="' . doku\DokuUtil::WikiLinkGet(doku\DokuGlobal::NsidGet()) . '" method="post">';
            echo '  <input type="hidden" name="do"   value="admin" />';
            echo '  <input type="hidden" name="page" value="' . $this->getPluginName() . '_' . $this->_Page . '" />';
            doku\DokuXhtmlForm::SecTokenEcho();
            // Table
            echo admin\AdminXhtml::TableGet($this->_ArrayGet(), $this->_StylesAr());
            // Form
            echo '</form>';
        }
    }
    /* -------------------------------------------------------------------- */
    protected function _StylesAr() {
        return [ //
            'width:130px; height:35px; white-space:nowrap;', //
            '', //
            'width:130px; white-space:nowrap; text-align:center;', //
            'width:130px; white-space:nowrap; text-align:center;', //
            'width:130px; white-space:nowrap; text-align:center;', //
        ];
    }
    /* -------------------------------------------------------------------- */
    protected function _ArrayGet() {
        return [];
    }
    /* -------------------------------------------------------------------- */
}