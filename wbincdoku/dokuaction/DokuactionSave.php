<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\baseaction;
use workbook\wbincdoku\doku;
class DokuactionSave {
    /* -------------------------------------------------------------------- */
    public static function Event_COMMON_WIKIPAGE_SAVE_AfterExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            baseaction\BaseactionNsid::SaveAfterExec($Event->data['id']);
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_COMMON_WIKIPAGE_SAVE_BeforeExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            $out = baseaction\BaseactionNsid::SaveBeforeExecGet($Event->data['id'], $Event->data['newContent']);
            if ( ! empty($out)) {
                $Event->data['newContent'] = $out;
            }
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
}