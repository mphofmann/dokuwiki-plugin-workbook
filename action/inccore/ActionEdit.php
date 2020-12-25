<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
class ActionEdit {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_COMMON_PAGETPL_LOAD(Doku_Event $Event, $inPara) {
        if (wb_classnsget('base\Base') == '') return;
        try {
            $out = base\BaseActionEdit::TextareaContentGet();
            if (!empty($out)) {
                $Event->data['tpl'] = $out;
            }
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}