<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbinc\admin;
class DokuactionSave {
    /* -------------------------------------------------------------------- */
    public static function Event_Before_COMMON_WIKIPAGE_SAVE_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $out = action\ActionNsid::SaveBeforeExecGet($Event->data['id'], $Event->data['newContent']);
            if ( ! empty($out)) {
                $Event->data['newContent'] = $out;
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_After_COMMON_WIKIPAGE_SAVE_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            action\ActionNsid::SaveAfterExec($Event->data['id']);
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}