<?php
use workbook\wbinc\admin;
class admin_plugin_workbook_admin extends \DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return true;
    }
    /* -------------------------------------------------------------------- */
    public function getMenuIcon() {
        return DOKU_PLUGIN . 'workbook/admin.svg';
    }
    /* -------------------------------------------------------------------- */
    public function getMenuText($language) {
        return $this->getLang('menu_admin');
    }
    /* -------------------------------------------------------------------- */
    public function html() {
        echo '<h1>Administration</h1>';
        echo admin\AdminXhtml::MenuGet();
        $class = "adminitem\\Adminitem" . ucfirst(@$_REQUEST['wb_item']);
        $classns = \_Wb_::ClassNsGet($class);
        try {
            $classpath = $classns . $class;
            echo $classpath::Get();
        } catch (Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}