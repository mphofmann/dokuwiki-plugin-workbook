<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbincdoku\doku;
class DokuactionJobEnd {
    /* -------------------------------------------------------------------- */
    public static function Event_DOKUWIKI_DONE_AfterExec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            action\ActionJob::EndExec();
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_TPL_CONTENT_DISPLAY_BeforeExec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            $Event->preventDefault();
            $Event->stopPropagation();
            $Event->result = false; // no caching
            $out = action\ActionContent::BodyAreaGet($Event->data);
            // TODO
            echo action\ActionContent::AdjustGet($out);
            $Event->data = '';
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
}