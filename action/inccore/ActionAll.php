<?php
namespace workbook\action\inccore;
use Doku_Event;
use workbookcore\wbinc\base;
class ActionAll {
    /* -------------------------------------------------------------------- */
    public static function EventBefore_ACTION_ACT_PREPROCESS(Doku_Event $Event, $inPara) {
        $Event->data = base\BaseActionAll::Before_ACTION_ACT_PREPROCESS($Event->data);
        return $Event;
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_AUTH_ACL_CHECK(Doku_Event $Event, $inPara) {
        $rc = base\BaseActionAll::Before_AUTH_ACL_CHECK($Event->data['id'], $Event->data['user'], $Event->data['groups']);
        if ($rc !== -1) {
            $Event->result = $rc;
            $Event->preventDefault();
        }
        return $Event->result;
    }
    /* -------------------------------------------------------------------- */
    public static function EventBefore_TPL_METAHEADER_OUTPUT(Doku_Event $Event, $inPara) {
        $Event->data = base\BaseActionAll::Before_TPL_METAHEADER_OUTPUT($Event->data); // $Event->data['link'][] = ['type' => 'text/css', 'rel' => 'stylesheet', 'href' => DOKU_BASE . 'lib/plugins/workbookcore/wbassets/fontawesome/free-5.15.1-web/css/all.css'];
    }
    /* -------------------------------------------------------------------- */
}