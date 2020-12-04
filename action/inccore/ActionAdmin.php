<?php
namespace workbook\action\inccore;
use Doku_Event;
class ActionAdmin {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_PLUGIN_CONFIG_PLUGINLIST(Doku_Event $Event, $inPara) {
        sort($Event->data);
    }
    /* -------------------------------------------------------------------- */
}