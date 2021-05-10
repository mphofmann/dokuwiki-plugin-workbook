<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
class DokuactionAdmin {
    /* -------------------------------------------------------------------- */
    public static function Event_After_PLUGIN_CONFIG_PLUGINLIST_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        sort($Event->data);
    }
    /* -------------------------------------------------------------------- */
}