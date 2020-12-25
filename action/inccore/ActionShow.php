<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbook\wbinc\admin;
use workbookcore\wbinc\base;
class ActionShow {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_RENDERER_CONTENT_POSTPROCESS(Doku_Event $Event, $inPara) { // new page content
        if (wb_classnsget('base\Base') == '') return;
        if ($Event->data[0] == 'xhtml') {
            if ($Event->data[1] == '1') $Event->data[1] = ''; // TODO Dokuwiki bug
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_CONTENT_DISPLAY(Doku_Event $Event, $inPara) { //
        if (wb_classnsget('base\Base') == '') return;
        try {
            $out = base\BaseActionShow::BodyAreaGet($Event->data);
            if (!empty($out)) $Event->data = $out;
        } catch (\Throwable $e) {
            admin\AdminXhtmlMsg::Echo('Warning', '', '', $e->getMessage());
        }
    }
    /* -------------------------------------------------------------------- */
}