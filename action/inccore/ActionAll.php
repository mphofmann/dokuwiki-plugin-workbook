<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionAll {
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_STARTED(Doku_Event $Event, $inPara) { // forward start to e.g. start_de
        if (workbookclassnsget('base\Base') == '') return;
        base\BaseActionAll::After_DOKUWIKI_STARTED_Get('');
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_ACTION_ACT_PREPROCESS(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base') == '') return;
        $Event->data = base\BaseActionAll::Before_ACTION_ACT_PREPROCESS($Event->data);
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_AUTH_ACL_CHECK(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base') == '') return;
        $rc = base\BaseActionAll::Before_AUTH_ACL_CHECK($Event->data['id'], $Event->data['user'], $Event->data['groups']);
        if ($rc !== -1) {
            $Event->result = $rc;
            $Event->preventDefault();
        }
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_METAHEADER_OUTPUT(Doku_Event $Event, $inPara) {
        if (workbookclassnsget('base\Base') == '') return;
        $Event->data = base\BaseActionAll::Before_TPL_METAHEADER_OUTPUT($Event->data); // $Event->data['link'][] = ['type' => 'text/css', 'rel' => 'stylesheet', 'href' => DOKU_BASE . 'lib/plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/css/all.css'];
    }
    /* -------------------------------------------------------------------- */
    public static function EventAfter_DOKUWIKI_DONE(Doku_Event $Event, $inPara) { //
        // echo "<pre>".print_r($_SESSION,true)."</pre>";
    }
    /* -------------------------------------------------------------------- */
}