<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionEdit {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_COMMON_PAGETPL_LOAD(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base') == '') return;
        $out = base\BaseActionEdit::After_COMMON_PAGETPL_LOAD('');
        if (!empty($out)) {
            $Event->data['tpl'] = $out;
        }
    }
    /* -------------------------------------------------------------------- */
}