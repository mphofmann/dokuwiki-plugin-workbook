<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\baseaction;
use workbook\wbincdoku\doku;
class DokuactionEdit {
    /* -------------------------------------------------------------------- */
    public static function Event_COMMON_PAGETPL_LOAD_AfterExec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::RunmodeCheck('module-workbook')) return;
        try {
            $out = baseaction\BaseactionEdit::TextareaContentGet(@$_REQUEST['template'], @$_REQUEST['input']);
            if ( ! empty($out)) {
                $Event->data['tpl'] = $out;
            }
        } catch (\Throwable $t) {
            doku\DokuAreaMsg::ThrowableAdd('Warning', $t);
        }
    }
    /* -------------------------------------------------------------------- */
}