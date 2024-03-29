<?php
use workbook\wbinc\baseadmin;
use workbook\wbincdoku\doku;
class admin_plugin_workbook_admin extends \DokuWiki_Admin_Plugin {
    /* -------------------------------------------------------------------- */
    public function forAdminOnly() {
        return false;
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
        echo baseadmin\BaseadminXhtml::MenuGet();
        $class = "mainadmin\\Mainadmin" . ucfirst(@$_REQUEST['wb_main']);
        $classns = \_Wb_::ClassNsGet($class);
        try {
            $classpath = $classns . $class;
            echo $classpath::Get();
        } catch (Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
}