<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbinc\admin;
class DokuactionJobEnd {
    /* -------------------------------------------------------------------- */
    public static function Event_Before_TPL_CONTENT_DISPLAY_Exec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $Event->preventDefault();
            $Event->stopPropagation();
            $Event->result = false; // no caching
            $out = action\ActionContent::BodyAreaGet($Event->data);
            echo action\ActionContent::AdjustGet($out);
            $Event->data = '';
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_After_DOKUWIKI_DONE_Exec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            action\ActionJob::EndExec();
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}