<?php
namespace workbook\wbincdoku\dokuaction;
use Doku_Event;
use workbook\wbinc\action;
use workbook\wbinc\admin;
class DokuactionEdit {
    /* -------------------------------------------------------------------- */
    public static function Event_After_COMMON_PAGETPL_LOAD_Exec(Doku_Event $Event, $inPara): void {
        if ( ! \_Wb_::ClassExists('action\ActionAction')) return;
        try {
            $out = action\ActionEdit::TextareaContentGet(@$_REQUEST['template'], @$_REQUEST['input']);
            if ( ! empty($out)) {
                $Event->data['tpl'] = $out;
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}