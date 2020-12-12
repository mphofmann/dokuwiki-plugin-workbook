<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionShow {
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_CONTENT_DISPLAY(Doku_Event $Event, $inPara) { //
        if (workbookclassnsget('base\Base') == '') return;
        $out = base\BaseActionShow::Before_TPL_CONTENT_DISPLAY_Get($Event->data);
        if (!empty($out)) $Event->data = $out;
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_RENDERER_CONTENT_POSTPROCESS(Doku_Event $Event, $inPara) { // new page content
        if (workbookclassnsget('base\Base') == '') return;
        if ($Event->data[0] == 'xhtml') {
            $Event->data[1] = base\BaseActionShow::After_RENDERER_CONTENT_POSTPROCESS_Get($Event->data[1]);
        }
    }
    /* -------------------------------------------------------------------- */
}