<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\admin;
class DokuactionAdmin {
    /* -------------------------------------------------------------------- */
    public static function Event_PLUGIN_CONFIG_PLUGINLIST_AfterExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        sort($Event->data);
        // Updates lib/plugin/workbook/conf/defaults.php
        if (substr($_REQUEST['page'], 0, 6) == 'config') {
            admin\AdminConf::ConfDefaultUpdate();
        }
    }
    /* -------------------------------------------------------------------- */
}