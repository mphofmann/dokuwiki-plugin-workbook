<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionShow {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_STARTED(Doku_Event $Event, $inPara) { // forward start to e.g. start_de
        base\BaseActionShow::Before_DOKUWIKI_STARTED_Get('');
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_DONE(Doku_Event $Event, $inPara) { //
        // echo "<pre>".print_r($_SESSION,true)."</pre>";
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_CONTENT_DISPLAY(Doku_Event $Event, $inPara) { //
        $out = base\BaseActionShow::Before_TPL_CONTENT_DISPLAY_Get($Event->data);
        if (!empty($out)) $Event->data = $out;
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_RENDERER_CONTENT_POSTPROCESS(Doku_Event $Event, $inPara) { // new page content
        if ($Event->data[0] == 'xhtml') {
            $Event->data[1] = base\BaseActionShow::After_RENDERER_CONTENT_POSTPROCESS_Get($Event->data[1]);
        }
    }
    /* -------------------------------------------------------------------- */
}