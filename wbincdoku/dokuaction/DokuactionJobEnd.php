<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\baseaction;
use workbook\wbincdoku\doku;
class DokuactionJobEnd {
    /* -------------------------------------------------------------------- */
    public static function Event_DOKUWIKI_DONE_AfterExec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            baseaction\BaseactionJob::JobEndExec();
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
    public static function Event_TPL_CONTENT_DISPLAY_BeforeExec(Doku_Event $Event, $inPara): void { //
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            // $Event->preventDefault();
            // $Event->stopPropagation();
            $Event->result = false; // no caching
            $out = $Event->data;
            $out = baseaction\BaseactionBody::AreaGet($out);
            $out = baseaction\BaseactionXhtml::AdjustGet($out);
            $Event->data = $out;
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
}